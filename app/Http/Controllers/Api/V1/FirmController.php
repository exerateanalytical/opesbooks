<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Firm;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmController extends Controller
{
    // ── Portfolio ────────────────────────────────────────────────────────────

    /** GET /api/v1/firm/portfolio */
    public function portfolio(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) {
            return response()->json(['message' => 'No firm associated with this account.'], 404);
        }

        $clients = $firm->activeClients()
            ->with(['members' => fn($q) => $q->where('users.id', fn($sub) => $sub)])
            ->get()
            ->map(fn(Company $c) => $this->clientPayload($c, $firm));

        // Aggregate stats
        $totalClients  = $clients->count();
        $overdue       = $clients->where('compliance.overall', 'OVERDUE')->count();
        $dgiPending    = $clients->where('compliance.dgi', 'PENDING')->count();

        return response()->json([
            'firm'    => $this->firmPayload($firm),
            'stats'   => [
                'total_clients'    => $totalClients,
                'overdue_filings'  => $overdue,
                'dgi_sync_pending' => $dgiPending,
            ],
            'clients' => $clients->values(),
        ]);
    }

    // ── Tasks / Deadline calendar ────────────────────────────────────────────

    /** GET /api/v1/firm/tasks */
    public function tasks(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) {
            return response()->json(['message' => 'No firm associated.'], 404);
        }

        $now    = now();
        $month  = (int) $now->format('m');
        $year   = (int) $now->format('Y');

        // Build canonical Cameroon tax calendar for the next 90 days
        $tasks = collect();

        foreach ($firm->activeClients()->get() as $company) {
            $compliance = $this->computeCompliance($company);

            // TVA — 15th of each month (previous month declaration)
            $tvaDate = \Carbon\Carbon::create($year, $month, 15);
            if ($tvaDate->isPast()) $tvaDate->addMonth();
            $tasks->push([
                'date'        => $tvaDate->toDateString(),
                'type'        => 'TVA',
                'label'       => 'Déclaration TVA ' . $tvaDate->subMonth()->locale('fr')->isoFormat('MMMM YYYY'),
                'company_id'  => $company->id,
                'company'     => $company->name,
                'niu'         => $company->niu,
                'status'      => $compliance['tva'] === 'CURRENT' ? 'DONE' : ($compliance['tva'] === 'OVERDUE' ? 'OVERDUE' : 'PENDING'),
                'urgency'     => $tvaDate->diffInDays(now()) <= 3 ? 'HIGH' : ($tvaDate->diffInDays(now()) <= 7 ? 'MEDIUM' : 'LOW'),
            ]);

            // CNPS — quarterly (Jan, Apr, Jul, Oct — 15th)
            $cnpsMonths = [1, 4, 7, 10];
            foreach ($cnpsMonths as $m) {
                $cnpsDate = \Carbon\Carbon::create($year, $m, 15);
                if ($cnpsDate->isPast()) $cnpsDate->addYear();
                if ($cnpsDate->diffInDays($now) <= 90) {
                    $tasks->push([
                        'date'        => $cnpsDate->toDateString(),
                        'type'        => 'CNPS',
                        'label'       => 'Cotisations CNPS T' . ceil($m / 3) . ' ' . $year,
                        'company_id'  => $company->id,
                        'company'     => $company->name,
                        'niu'         => $company->niu,
                        'status'      => 'PENDING',
                        'urgency'     => $cnpsDate->diffInDays(now()) <= 7 ? 'HIGH' : 'LOW',
                    ]);
                    break;
                }
            }

            // DSF — January 31 each year
            $dsfDate = \Carbon\Carbon::create($year, 1, 31);
            if ($dsfDate->isPast()) $dsfDate->addYear();
            if ($dsfDate->diffInDays($now) <= 90) {
                $tasks->push([
                    'date'        => $dsfDate->toDateString(),
                    'type'        => 'DSF',
                    'label'       => 'Déclaration Statistique et Fiscale ' . ($year - 1),
                    'company_id'  => $company->id,
                    'company'     => $company->name,
                    'niu'         => $company->niu,
                    'status'      => 'PENDING',
                    'urgency'     => $dsfDate->diffInDays(now()) <= 14 ? 'HIGH' : 'LOW',
                ]);
            }
        }

        // Group by date, sort ascending
        $grouped = $tasks
            ->sortBy('date')
            ->groupBy('date')
            ->map(fn($group, $date) => [
                'date'    => $date,
                'label'   => \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY'),
                'entries' => $group->values(),
            ])
            ->values();

        return response()->json(['tasks' => $grouped]);
    }

    // ── Client management ────────────────────────────────────────────────────

    /** POST /api/v1/firm/clients — add a company to the firm portfolio */
    public function addClient(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);
        if ($firm->isAtCapacity()) return response()->json(['message' => 'Firm client limit reached.'], 422);

        $data = $request->validate([
            'company_id'       => 'required|integer|exists:companies,id',
            'engagement_type'  => 'in:FULL_OUTSOURCE,REVIEW_ONLY,TAX_ONLY,PAYROLL_ONLY',
            'billing_mode'     => 'in:FIRM_PAYS,CLIENT_PAYS,HYBRID',
            'notes'            => 'nullable|string|max:1000',
            'assigned_accountant_id' => 'nullable|integer|exists:users,id',
        ]);

        $already = $firm->clients()->where('companies.id', $data['company_id'])->exists();
        if ($already) {
            return response()->json(['message' => 'Company already in portfolio.'], 409);
        }

        $firm->clients()->attach($data['company_id'], [
            'engagement_type'         => $data['engagement_type'] ?? 'FULL_OUTSOURCE',
            'billing_mode'            => $data['billing_mode'] ?? 'FIRM_PAYS',
            'notes'                   => $data['notes'] ?? null,
            'assigned_accountant_id'  => $data['assigned_accountant_id'] ?? null,
            'is_active'               => true,
            'onboarded_at'            => now(),
        ]);

        // Grant the firm's staff ACCOUNTANT access to this company via company_user
        $firm->activeStaff()->each(function (User $staff) use ($data) {
            if (! $staff->belongsToCompany($data['company_id'])) {
                $staff->companies()->attach($data['company_id'], [
                    'role'       => 'ACCOUNTANT',
                    'is_default' => false,
                ]);
            }
        });

        $company = Company::findOrFail($data['company_id']);

        return response()->json([
            'message' => 'Client added to portfolio.',
            'client'  => $this->clientPayload($company, $firm),
        ], 201);
    }

    /** DELETE /api/v1/firm/clients/{company} */
    public function removeClient(Request $request, Company $company): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $firm->clients()->detach($company->id);

        return response()->json(['message' => 'Client removed from portfolio.']);
    }

    /** PUT /api/v1/firm/clients/{company} — update engagement details */
    public function updateClient(Request $request, Company $company): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $data = $request->validate([
            'engagement_type'        => 'in:FULL_OUTSOURCE,REVIEW_ONLY,TAX_ONLY,PAYROLL_ONLY',
            'billing_mode'           => 'in:FIRM_PAYS,CLIENT_PAYS,HYBRID',
            'notes'                  => 'nullable|string|max:1000',
            'assigned_accountant_id' => 'nullable|integer|exists:users,id',
        ]);

        $firm->clients()->updateExistingPivot($company->id, $data);

        return response()->json([
            'message' => 'Client updated.',
            'client'  => $this->clientPayload($company->fresh(), $firm),
        ]);
    }

    /** POST /api/v1/firm/clients/{company}/open — switch active company and return context */
    public function openClient(Request $request, Company $company): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $inPortfolio = $firm->clients()->where('companies.id', $company->id)->exists();
        if (! $inPortfolio) return response()->json(['message' => 'Company not in firm portfolio.'], 403);

        // Ensure the accountant has access to this company
        if (! $user->belongsToCompany($company->id)) {
            $user->companies()->attach($company->id, ['role' => 'ACCOUNTANT', 'is_default' => false]);
        }

        $user->forceFill([
            'company_id' => $company->id,
            'role'       => 'FIRM_ACCOUNTANT',
        ])->save();

        return response()->json([
            'message'  => 'Switched to client.',
            'company'  => $company->toArray(),
            'firm'     => $this->firmPayload($firm),
            'role'     => 'FIRM_ACCOUNTANT',
        ]);
    }

    // ── Firm setup ───────────────────────────────────────────────────────────

    /** POST /api/v1/firm — create a new firm (for the current user) */
    public function create(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'oecam_number' => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
            'city'         => 'nullable|string|max:100',
        ]);

        $firm = Firm::create([
            'name'         => $data['name'],
            'slug'         => Str::slug($data['name']) . '-' . Str::random(4),
            'oecam_number' => $data['oecam_number'] ?? null,
            'email'        => $data['email'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'address'      => $data['address'] ?? null,
            'city'         => $data['city'] ?? 'Douala',
        ]);

        // Attach creator as PARTNER
        $firm->staff()->attach($user->id, ['firm_role' => 'PARTNER', 'is_active' => true]);

        // Upgrade user role
        $user->forceFill(['role' => 'FIRM_ACCOUNTANT'])->save();

        return response()->json([
            'message' => 'Firm created.',
            'firm'    => $this->firmPayload($firm),
        ], 201);
    }

    /** GET /api/v1/firm/me — current firm info for the authenticated user */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) {
            return response()->json(['firm' => null, 'is_firm_accountant' => false]);
        }

        return response()->json([
            'firm'              => $this->firmPayload($firm),
            'is_firm_accountant' => true,
            'firm_role'         => $firm->pivot?->firm_role ?? 'JUNIOR',
        ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function firmPayload(Firm $firm): array
    {
        return [
            'id'           => $firm->id,
            'name'         => $firm->name,
            'slug'         => $firm->slug,
            'oecam_number' => $firm->oecam_number,
            'email'        => $firm->email,
            'phone'        => $firm->phone,
            'city'         => $firm->city,
            'logo_url'     => $firm->logo_path ? Storage::url($firm->logo_path) : null,
            'max_clients'  => $firm->max_clients,
            'client_count' => $firm->clientCount(),
        ];
    }

    private function clientPayload(Company $company, Firm $firm): array
    {
        $pivot      = $firm->clients()->where('companies.id', $company->id)->first()?->pivot;
        $compliance = $this->computeCompliance($company);

        $assignee = $pivot?->assigned_accountant_id
            ? User::find($pivot->assigned_accountant_id)?->name
            : null;

        $lastEntry = JournalEntry::where('company_id', $company->id)
            ->latest('posting_date')
            ->first();

        return [
            'id'                  => $company->id,
            'name'                => $company->name,
            'niu'                 => $company->niu,
            'rccm'                => $company->rccm,
            'tax_regime'          => $company->tax_regime,
            'subscription_status' => $company->subscription_status,
            'plan'                => $company->plan_slug ?? 'STARTER',
            'logo_url'            => $company->logo_path ? Storage::url($company->logo_path) : null,
            'engagement_type'     => $pivot?->engagement_type ?? 'FULL_OUTSOURCE',
            'billing_mode'        => $pivot?->billing_mode ?? 'FIRM_PAYS',
            'assigned_accountant' => $assignee,
            'notes'               => $pivot?->notes,
            'onboarded_at'        => $pivot?->onboarded_at,
            'last_activity'       => $lastEntry?->posting_date,
            'last_activity_human' => $lastEntry ? $lastEntry->posting_date->diffForHumans() : null,
            'compliance'          => $compliance,
        ];
    }

    private function computeCompliance(Company $company): array
    {
        // TVA — check for journal lines on account 443xxx (TVA Facturée) in last 45 days
        $lastTvaLine = JournalLine::whereHas('entry', fn($q) => $q->where('company_id', $company->id))
            ->where('account_code', 'like', '443%')
            ->orderByDesc('created_at')
            ->first();

        $tvaStatus = 'UNKNOWN';
        if ($lastTvaLine) {
            $days = $lastTvaLine->created_at->diffInDays(now());
            $tvaStatus = $days <= 30 ? 'CURRENT' : ($days <= 45 ? 'DUE' : 'OVERDUE');
        } elseif (JournalEntry::where('company_id', $company->id)->exists()) {
            // Company has entries but none with TVA — likely Libératoire regime
            $tvaStatus = $company->tax_regime === 'LIBERATOIRE' ? 'N/A' : 'UNKNOWN';
        }

        // DGI sync — count unsynced entries
        $dgiPending = JournalEntry::where('company_id', $company->id)
            ->where('dgi_sync_status', 'PENDING')
            ->count();
        $dgiStatus = $dgiPending > 0 ? 'PENDING' : 'SYNCED';

        // DSF — check if it's past Jan 31 of the current year (no tracking yet — mark as UNKNOWN)
        $dsfStatus = 'UNKNOWN';
        if (now()->month >= 2 && now()->month <= 4) {
            $dsfStatus = 'DUE'; // Filing season: Feb-Apr
        } elseif (now()->month > 4) {
            $dsfStatus = 'CURRENT'; // Filed (assumed)
        }

        // Overall
        $overall = 'OK';
        if (in_array('OVERDUE', [$tvaStatus])) $overall = 'OVERDUE';
        elseif (in_array('DUE', [$tvaStatus, $dsfStatus]) || $dgiPending > 3) $overall = 'WARNING';

        return [
            'tva'          => $tvaStatus,
            'dgi'          => $dgiStatus,
            'dgi_pending'  => $dgiPending,
            'dsf'          => $dsfStatus,
            'cnps'         => 'UNKNOWN',
            'overall'      => $overall,
        ];
    }
}
