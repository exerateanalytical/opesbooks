<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Firm;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\SyscohadaAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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

        $companies = $firm->activeClients()->get();

        // Fix #7: batch all compliance queries to avoid N+1
        $complianceMap   = $this->batchCompliance($companies);
        $lastActivityMap = $this->batchLastActivity($companies->pluck('id')->all());

        // Resolve pivot data and assigned accountant names in bulk
        $pivotMap = $firm->clients()
            ->withPivot(['assigned_accountant_id', 'engagement_type', 'billing_mode', 'notes', 'is_active', 'onboarded_at', 'locked_until'])
            ->get()->keyBy('id');

        $accountantIds = $pivotMap->pluck('pivot.assigned_accountant_id')->filter()->unique()->values()->all();
        $accountants   = User::whereIn('id', $accountantIds)->pluck('name', 'id');

        $clients = $companies->map(function (Company $c) use ($complianceMap, $lastActivityMap, $pivotMap, $accountants) {
            $pivot    = $pivotMap[$c->id]?->pivot ?? null;
            $comp     = $complianceMap[$c->id] ?? $this->emptyCompliance();
            $lastAt   = $lastActivityMap[$c->id] ?? null;
            $assignee = $pivot?->assigned_accountant_id ? ($accountants[$pivot->assigned_accountant_id] ?? null) : null;

            return [
                'id'                  => $c->id,
                'name'                => $c->name,
                'niu'                 => $c->niu,
                'rccm'                => $c->rccm,
                'tax_regime'          => $c->tax_regime,
                'subscription_status' => $c->subscription_status,
                'plan'                => $c->plan_slug ?? 'STARTER',
                'logo_url'            => $c->logo_path ? Storage::url($c->logo_path) : null,
                'engagement_type'     => $pivot?->engagement_type ?? 'FULL_OUTSOURCE',
                'billing_mode'        => $pivot?->billing_mode ?? 'FIRM_PAYS',
                'assigned_accountant' => $assignee,
                'notes'               => $pivot?->notes,
                'onboarded_at'        => $pivot?->onboarded_at,
                'locked_until'        => $pivot?->locked_until,
                'last_activity'       => $lastAt,
                'last_activity_human' => $lastAt ? \Carbon\Carbon::parse($lastAt)->diffForHumans() : null,
                'compliance'          => $comp,
            ];
        });

        $overdue    = $clients->where('compliance.overall', 'OVERDUE')->count();
        $dgiPending = $clients->where('compliance.dgi', 'PENDING')->count();

        return response()->json([
            'firm'    => $this->firmPayload($firm),
            'stats'   => [
                'total_clients'    => $clients->count(),
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

        $now   = now();
        $month = (int) $now->format('m');
        $year  = (int) $now->format('Y');

        $tasks    = collect();
        $clients  = $firm->activeClients()->get();

        // Fix #7: batch compliance for urgency calculation
        $complianceMap = $this->batchCompliance($clients);

        foreach ($clients as $company) {
            $compliance = $complianceMap[$company->id] ?? $this->emptyCompliance();

            // Fix #6: skip TVA tasks for libératoire regime companies
            if ($company->tax_regime !== 'LIBERATOIRE') {
                $tvaDate = \Carbon\Carbon::create($year, $month, 15);
                if ($tvaDate->isPast()) $tvaDate->addMonth();
                $tasks->push([
                    'date'       => $tvaDate->toDateString(),
                    'type'       => 'TVA',
                    'label'      => 'Déclaration TVA ' . $tvaDate->copy()->subMonth()->locale('fr')->isoFormat('MMMM YYYY'),
                    'company_id' => $company->id,
                    'company'    => $company->name,
                    'niu'        => $company->niu,
                    'status'     => $compliance['tva'] === 'CURRENT' ? 'DONE' : ($compliance['tva'] === 'OVERDUE' ? 'OVERDUE' : 'PENDING'),
                    'urgency'    => $tvaDate->diffInDays(now()) <= 3 ? 'HIGH' : ($tvaDate->diffInDays(now()) <= 7 ? 'MEDIUM' : 'LOW'),
                ]);
            }

            // CNPS — quarterly (Jan, Apr, Jul, Oct — 15th)
            foreach ([1, 4, 7, 10] as $m) {
                $cnpsDate = \Carbon\Carbon::create($year, $m, 15);
                if ($cnpsDate->isPast()) $cnpsDate->addYear();
                if ($cnpsDate->diffInDays($now) <= 90) {
                    $tasks->push([
                        'date'       => $cnpsDate->toDateString(),
                        'type'       => 'CNPS',
                        'label'      => 'Cotisations CNPS T' . ceil($m / 3) . ' ' . $year,
                        'company_id' => $company->id,
                        'company'    => $company->name,
                        'niu'        => $company->niu,
                        'status'     => 'PENDING',
                        'urgency'    => $cnpsDate->diffInDays(now()) <= 7 ? 'HIGH' : 'LOW',
                    ]);
                    break;
                }
            }

            // DSF — January 31 each year
            $dsfDate = \Carbon\Carbon::create($year, 1, 31);
            if ($dsfDate->isPast()) $dsfDate->addYear();
            if ($dsfDate->diffInDays($now) <= 90) {
                $tasks->push([
                    'date'       => $dsfDate->toDateString(),
                    'type'       => 'DSF',
                    'label'      => 'Déclaration Statistique et Fiscale ' . ($year - 1),
                    'company_id' => $company->id,
                    'company'    => $company->name,
                    'niu'        => $company->niu,
                    'status'     => 'PENDING',
                    'urgency'    => $dsfDate->diffInDays(now()) <= 14 ? 'HIGH' : 'LOW',
                ]);
            }
        }

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

    /** POST /api/v1/firm/clients */
    public function addClient(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);
        if ($firm->isAtCapacity()) return response()->json(['message' => 'Firm client limit reached.'], 422);

        $data = $request->validate([
            'company_id'             => 'required|integer|exists:companies,id',
            'engagement_type'        => 'in:FULL_OUTSOURCE,REVIEW_ONLY,TAX_ONLY,PAYROLL_ONLY',
            'billing_mode'           => 'in:FIRM_PAYS,CLIENT_PAYS,HYBRID',
            'notes'                  => 'nullable|string|max:5000',
            'assigned_accountant_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($firm->clients()->where('companies.id', $data['company_id'])->exists()) {
            return response()->json(['message' => 'Company already in portfolio.'], 409);
        }

        $firm->clients()->attach($data['company_id'], [
            'engagement_type'        => $data['engagement_type'] ?? 'FULL_OUTSOURCE',
            'billing_mode'           => $data['billing_mode'] ?? 'FIRM_PAYS',
            'notes'                  => $data['notes'] ?? null,
            'assigned_accountant_id' => $data['assigned_accountant_id'] ?? null,
            'is_active'              => true,
            'onboarded_at'           => now(),
        ]);

        // Grant all active firm staff access to this new client company
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

        if (! $firm->clients()->where('companies.id', $company->id)->exists()) {
            return response()->json(['message' => 'Company not in portfolio.'], 404);
        }

        // Fix #1: revoke all firm staff access to this company before detaching
        $firm->staff()->each(function (User $staff) use ($company) {
            $staff->companies()->detach($company->id);
        });

        $firm->clients()->detach($company->id);

        return response()->json(['message' => 'Client removed from portfolio.']);
    }

    /** PUT /api/v1/firm/clients/{company} */
    public function updateClient(Request $request, Company $company): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $data = $request->validate([
            'engagement_type'        => 'in:FULL_OUTSOURCE,REVIEW_ONLY,TAX_ONLY,PAYROLL_ONLY',
            'billing_mode'           => 'in:FIRM_PAYS,CLIENT_PAYS,HYBRID',
            'notes'                  => 'nullable|string|max:5000',
            'assigned_accountant_id' => 'nullable|integer|exists:users,id',
        ]);

        $firm->clients()->updateExistingPivot($company->id, $data);

        return response()->json([
            'message' => 'Client updated.',
            'client'  => $this->clientPayload($company->fresh(), $firm),
        ]);
    }

    /** POST /api/v1/firm/clients/{company}/open — switch active company context */
    public function openClient(Request $request, Company $company): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $inPortfolio = $firm->clients()->where('companies.id', $company->id)->exists();
        if (! $inPortfolio) return response()->json(['message' => 'Company not in firm portfolio.'], 403);

        // Fix #14: JUNIOR/ASSISTANT can only open clients assigned to them
        $firmPivot = $firm->pivot;
        if (! in_array($firmPivot?->firm_role, ['PARTNER', 'SENIOR'])) {
            $clientPivot = $firm->clients()->where('companies.id', $company->id)->first()?->pivot;
            $assignedId  = $clientPivot?->assigned_accountant_id;
            if ($assignedId && $assignedId !== $user->id) {
                return response()->json(['message' => 'Ce dossier est assigné à un autre collaborateur.'], 403);
            }
        }

        // Ensure accountant access to the company
        if (! $user->belongsToCompany($company->id)) {
            $user->companies()->attach($company->id, ['role' => 'ACCOUNTANT', 'is_default' => false]);
        }

        // Fix #5: preserve the user's home company before switching
        if (! $user->home_company_id) {
            $user->home_company_id = $user->company_id;
        }
        $user->company_id = $company->id;
        $user->save();

        $clientPivot = $firm->clients()->where('companies.id', $company->id)->first()?->pivot;

        return response()->json([
            'message'         => 'Switched to client.',
            'company'         => $company->toArray(),
            'firm'            => $this->firmPayload($firm),
            'role'            => 'FIRM_ACCOUNTANT',
            'engagement_type' => $clientPivot?->engagement_type ?? 'FULL_OUTSOURCE',
            'locked_until'    => $clientPivot?->locked_until,
        ]);
    }

    /** POST /api/v1/firm/clients/close — return to firm's own context */
    public function closeClient(Request $request): JsonResponse
    {
        $user = $request->user();

        $homeId = $user->home_company_id;

        $user->forceFill([
            'company_id'      => $homeId,
            'home_company_id' => null,
        ])->save();

        return response()->json([
            'message'    => 'Returned to firm context.',
            'company_id' => $homeId,
        ]);
    }

    /** POST /api/v1/firm/clients/{company}/lock — lock a fiscal period */
    public function lockPeriod(Request $request, Company $company): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $firmPivot = $firm->pivot;
        if (! in_array($firmPivot?->firm_role, ['PARTNER', 'SENIOR'])) {
            return response()->json(['message' => 'Seuls les associés et seniors peuvent clôturer une période.'], 403);
        }

        if (! $firm->clients()->where('companies.id', $company->id)->exists()) {
            return response()->json(['message' => 'Company not in portfolio.'], 403);
        }

        $data = $request->validate(['locked_until' => 'required|date']);

        $firm->clients()->updateExistingPivot($company->id, ['locked_until' => $data['locked_until']]);

        return response()->json(['message' => 'Période clôturée jusqu\'au ' . $data['locked_until']]);
    }

    /** GET /api/v1/firm/companies/search?q= — search companies not yet in portfolio */
    public function searchCompanies(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) return response()->json(['companies' => []]);

        $alreadyInPortfolio = $firm
            ? $firm->clients()->pluck('companies.id')
            : collect();

        $companies = Company::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('niu', 'like', "%{$q}%");
            })
            ->whereNotIn('id', $alreadyInPortfolio)
            ->limit(10)
            ->get(['id', 'name', 'niu', 'tax_regime', 'subscription_status']);

        return response()->json(['companies' => $companies]);
    }

    // ── Firm setup ───────────────────────────────────────────────────────────

    /** POST /api/v1/firm */
    public function create(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->primaryFirm()) {
            return response()->json(['message' => 'You already belong to a firm.'], 409);
        }

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

        $firm->staff()->attach($user->id, ['firm_role' => 'PARTNER', 'is_active' => true]);

        // Fix #3: only set FIRM_ACCOUNTANT if not already a privileged role
        if (! in_array($user->role, ['OWNER', 'SUPER_ADMIN', 'FIRM_ACCOUNTANT'])) {
            $user->forceFill(['role' => 'FIRM_ACCOUNTANT'])->save();
        }

        return response()->json([
            'message' => 'Firm created.',
            'firm'    => $this->firmPayload($firm),
        ], 201);
    }

    /** PUT /api/v1/firm */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $firmPivot = $firm->pivot;
        if (! in_array($firmPivot?->firm_role, ['PARTNER', 'SENIOR'])) {
            return response()->json(['message' => 'Only PARTNER or SENIOR staff can update firm settings.'], 403);
        }

        $data = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'oecam_number' => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
            'city'         => 'nullable|string|max:100',
        ]);

        $firm->fill($data)->save();

        return response()->json([
            'message' => 'Firm updated.',
            'firm'    => $this->firmPayload($firm->fresh()),
        ]);
    }

    /** POST /api/v1/firm/logo */
    public function uploadLogo(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $request->validate(['logo' => 'required|image|mimes:jpeg,png,webp|max:2048']);

        if ($firm->logo_path) {
            Storage::disk('public')->delete($firm->logo_path);
        }

        $path = $request->file('logo')->store('firm-logos', 'public');
        $firm->update(['logo_path' => $path]);

        return response()->json([
            'message'  => 'Logo updated.',
            'logo_url' => Storage::url($path),
        ]);
    }

    // ── Consolidated report ──────────────────────────────────────────────────

    /** GET /api/v1/firm/report?from=YYYY-MM-DD&to=YYYY-MM-DD */
    public function report(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $from = $request->query('from', now()->startOfYear()->toDateString());
        $to   = $request->query('to',   now()->toDateString());

        $clientIds = $firm->activeClients()->pluck('companies.id');

        $revenueIds = SyscohadaAccount::where('code', 'like', '7%')->pluck('id');
        $tvaIds     = SyscohadaAccount::where('code', 'like', '443%')->pluck('id');
        $chargeIds  = SyscohadaAccount::where('code', 'like', '6%')->pluck('id');

        $clients = $firm->activeClients()->get()->map(function (Company $c) use ($from, $to, $revenueIds, $tvaIds, $chargeIds) {
            $lines = JournalLine::whereHas('journalEntry', fn($q) =>
                    $q->where('company_id', $c->id)->whereBetween('posting_date', [$from, $to])
                )->get();

            $revenue = $lines->whereIn('syscohada_account_id', $revenueIds->all())
                             ->sum(fn($l) => $l->credit - $l->debit);
            $tva     = $lines->whereIn('syscohada_account_id', $tvaIds->all())
                             ->sum(fn($l) => $l->credit - $l->debit);
            $charges = $lines->whereIn('syscohada_account_id', $chargeIds->all())
                             ->sum(fn($l) => $l->debit - $l->credit);

            return [
                'id'      => $c->id,
                'name'    => $c->name,
                'niu'     => $c->niu,
                'revenue' => round($revenue, 0),
                'tva'     => round($tva, 0),
                'charges' => round($charges, 0),
                'result'  => round($revenue - $charges, 0),
            ];
        });

        $dgiPending = JournalEntry::whereIn('company_id', $clientIds)
            ->where('dgi_sync_status', 'PENDING')
            ->count();

        return response()->json([
            'period'  => ['from' => $from, 'to' => $to],
            'totals'  => [
                'revenue'     => $clients->sum('revenue'),
                'tva'         => $clients->sum('tva'),
                'charges'     => $clients->sum('charges'),
                'result'      => $clients->sum('result'),
                'dgi_pending' => $dgiPending,
            ],
            'clients' => $clients->sortByDesc('revenue')->values(),
        ]);
    }

    // ── Activity log ─────────────────────────────────────────────────────────

    /** GET /api/v1/firm/activity */
    public function activity(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $clientIds    = $firm->activeClients()->pluck('companies.id');
        $companyNames = $firm->activeClients()->pluck('companies.name', 'companies.id');

        $entries = JournalEntry::whereIn('company_id', $clientIds)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'company_id', 'reference_id', 'memo', 'posting_date', 'dgi_sync_status', 'created_at'])
            ->map(fn($e) => [
                'id'          => $e->id,
                'company_id'  => $e->company_id,
                'company'     => $companyNames[$e->company_id] ?? '—',
                'reference'   => $e->reference_id,
                'memo'        => $e->memo,
                'date'        => $e->posting_date,
                'dgi_status'  => $e->dgi_sync_status,
                'recorded_at' => $e->created_at->diffForHumans(),
            ]);

        return response()->json(['activity' => $entries]);
    }

    // ── Staff management ─────────────────────────────────────────────────────

    /** GET /api/v1/firm/staff */
    public function staff(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $members = $firm->staff()->get()->map(fn(User $u) => [
            'id'        => $u->id,
            'name'      => $u->name,
            'email'     => $u->email,
            'firm_role' => $u->pivot->firm_role,
            'is_active' => (bool) $u->pivot->is_active,
        ]);

        return response()->json(['staff' => $members]);
    }

    /** POST /api/v1/firm/staff */
    public function addStaff(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        $data = $request->validate([
            'email'     => 'required|email|exists:users,email',
            'firm_role' => 'in:PARTNER,SENIOR,JUNIOR,ASSISTANT',
        ]);

        $target = User::where('email', $data['email'])->firstOrFail();

        if ($firm->staff()->where('users.id', $target->id)->exists()) {
            return response()->json(['message' => 'User is already a member of this firm.'], 409);
        }

        $firm->staff()->attach($target->id, [
            'firm_role' => $data['firm_role'] ?? 'JUNIOR',
            'is_active' => true,
        ]);

        // Fix #3: never overwrite OWNER or SUPER_ADMIN roles
        if (! in_array($target->role, ['OWNER', 'SUPER_ADMIN', 'FIRM_ACCOUNTANT'])) {
            $target->forceFill(['role' => 'FIRM_ACCOUNTANT'])->save();
        }

        // Grant access to all existing firm clients
        $firm->activeClients()->each(function (Company $company) use ($target) {
            if (! $target->belongsToCompany($company->id)) {
                $target->companies()->attach($company->id, ['role' => 'ACCOUNTANT', 'is_default' => false]);
            }
        });

        return response()->json([
            'message' => 'Staff member added.',
            'member'  => [
                'id'        => $target->id,
                'name'      => $target->name,
                'email'     => $target->email,
                'firm_role' => $data['firm_role'] ?? 'JUNIOR',
                'is_active' => true,
            ],
        ], 201);
    }

    /** PUT /api/v1/firm/staff/{user} — change a staff member's firm role */
    public function updateStaff(Request $request, User $staffUser): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);

        // Fix #4: only PARTNER can change firm roles
        $firmPivot = $firm->pivot;
        if ($firmPivot?->firm_role !== 'PARTNER') {
            return response()->json(['message' => 'Seuls les associés (PARTNER) peuvent modifier les rôles.'], 403);
        }

        // Fix #4: verify target actually belongs to this firm
        if (! $firm->staff()->where('users.id', $staffUser->id)->exists()) {
            return response()->json(['message' => 'User not in this firm.'], 404);
        }

        $data = $request->validate(['firm_role' => 'required|in:PARTNER,SENIOR,JUNIOR,ASSISTANT']);

        $firm->staff()->updateExistingPivot($staffUser->id, ['firm_role' => $data['firm_role']]);

        return response()->json([
            'message'   => 'Role updated.',
            'firm_role' => $data['firm_role'],
        ]);
    }

    /** DELETE /api/v1/firm/staff/{user} */
    public function removeStaff(Request $request, User $staffUser): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) return response()->json(['message' => 'No firm associated.'], 404);
        if ($staffUser->id === $user->id) return response()->json(['message' => 'Cannot remove yourself.'], 422);

        // Fix #4: verify target actually belongs to this firm
        if (! $firm->staff()->where('users.id', $staffUser->id)->exists()) {
            return response()->json(['message' => 'User not in this firm.'], 404);
        }

        // Fix #2: revoke access to all firm client companies before detaching
        $clientIds = $firm->activeClients()->pluck('companies.id')->all();
        if ($clientIds) {
            $staffUser->companies()->detach($clientIds);
        }

        $firm->staff()->detach($staffUser->id);

        return response()->json(['message' => 'Staff member removed.']);
    }

    /** GET /api/v1/firm/me */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $firm = $user->primaryFirm();

        if (! $firm) {
            return response()->json([
                'firm'               => null,
                'is_firm_accountant' => false,
                'in_client_context'  => false,
            ]);
        }

        return response()->json([
            'firm'               => $this->firmPayload($firm),
            'is_firm_accountant' => true,
            'firm_role'          => $firm->pivot?->firm_role ?? 'JUNIOR',
            // in_client_context: true if user has switched into a client company.
            // For pure firm accountants (home = null), any non-null company_id means we're inside a dossier.
            // For hybrid users (OWNER who joined a firm), home_company_id being set signals the switch.
            'in_client_context'  => $user->company_id !== null,
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
            'address'      => $firm->address,
            'city'         => $firm->city,
            'logo_url'     => $firm->logo_path ? Storage::url($firm->logo_path) : null,
            'max_clients'  => $firm->max_clients,
            'client_count' => $firm->clientCount(),
        ];
    }

    private function clientPayload(Company $company, Firm $firm): array
    {
        $pivot    = $firm->clients()->where('companies.id', $company->id)->first()?->pivot;
        $comp     = $this->computeComplianceSingle($company);
        $assignee = $pivot?->assigned_accountant_id
            ? User::find($pivot->assigned_accountant_id)?->name
            : null;
        $lastAt = JournalEntry::where('company_id', $company->id)->max('posting_date');

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
            'locked_until'        => $pivot?->locked_until,
            'last_activity'       => $lastAt,
            'last_activity_human' => $lastAt ? \Carbon\Carbon::parse($lastAt)->diffForHumans() : null,
            'compliance'          => $comp,
        ];
    }

    /** Batch compliance computation — avoids N+1 for portfolio listing (fix #7). */
    private function batchCompliance(Collection $companies): array
    {
        $companyIds    = $companies->pluck('id')->all();
        $tvaAccountIds = SyscohadaAccount::where('code', 'like', '443%')->pluck('id');

        // Batch: last TVA journal line timestamp per company
        $lastTvaLines = JournalLine::whereIn('syscohada_account_id', $tvaAccountIds)
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entries.company_id', $companyIds)
            ->selectRaw('journal_entries.company_id, MAX(journal_lines.created_at) as last_at')
            ->groupBy('journal_entries.company_id')
            ->pluck('last_at', 'company_id');

        // Batch: DGI pending count per company
        $dgiCounts = JournalEntry::whereIn('company_id', $companyIds)
            ->where('dgi_sync_status', 'PENDING')
            ->selectRaw('company_id, COUNT(*) as cnt')
            ->groupBy('company_id')
            ->pluck('cnt', 'company_id');

        $result = [];
        foreach ($companies as $company) {
            $result[$company->id] = $this->buildComplianceFromBatch(
                $company,
                $lastTvaLines[$company->id] ?? null,
                (int) ($dgiCounts[$company->id] ?? 0)
            );
        }
        return $result;
    }

    /** Single-company compliance for clientPayload() after add/update. */
    private function computeComplianceSingle(Company $company): array
    {
        $tvaAccountIds = SyscohadaAccount::where('code', 'like', '443%')->pluck('id');
        $lastTvaLine = JournalLine::whereHas('journalEntry', fn($q) => $q->where('company_id', $company->id))
            ->whereIn('syscohada_account_id', $tvaAccountIds)
            ->max('created_at');
        $dgiPending = JournalEntry::where('company_id', $company->id)
            ->where('dgi_sync_status', 'PENDING')
            ->count();

        return $this->buildComplianceFromBatch($company, $lastTvaLine, $dgiPending);
    }

    private function buildComplianceFromBatch(Company $company, ?string $lastTvaAt, int $dgiPending): array
    {
        if ($lastTvaAt) {
            $days = \Carbon\Carbon::parse($lastTvaAt)->diffInDays(now());
            $tvaStatus = $days <= 30 ? 'CURRENT' : ($days <= 45 ? 'DUE' : 'OVERDUE');
        } else {
            // Fix #6: N/A for libératoire regime instead of UNKNOWN
            $tvaStatus = $company->tax_regime === 'LIBERATOIRE' ? 'N/A' : 'UNKNOWN';
        }

        $dgiStatus = $dgiPending > 0 ? 'PENDING' : 'SYNCED';

        $dsfStatus = 'UNKNOWN';
        if (now()->month >= 2 && now()->month <= 4) $dsfStatus = 'DUE';
        elseif (now()->month > 4) $dsfStatus = 'CURRENT';

        $overall = 'OK';
        if ($tvaStatus === 'OVERDUE') $overall = 'OVERDUE';
        elseif (in_array($tvaStatus, ['DUE', 'UNKNOWN']) || $dgiPending > 3) $overall = 'WARNING';

        return [
            'tva'         => $tvaStatus,
            'dgi'         => $dgiStatus,
            'dgi_pending' => $dgiPending,
            'dsf'         => $dsfStatus,
            'cnps'        => 'UNKNOWN',
            'overall'     => $overall,
        ];
    }

    private function batchLastActivity(array $companyIds): array
    {
        return JournalEntry::whereIn('company_id', $companyIds)
            ->selectRaw('company_id, MAX(posting_date) as last_date')
            ->groupBy('company_id')
            ->pluck('last_date', 'company_id')
            ->all();
    }

    private function emptyCompliance(): array
    {
        return ['tva' => 'UNKNOWN', 'dgi' => 'SYNCED', 'dgi_pending' => 0, 'dsf' => 'UNKNOWN', 'cnps' => 'UNKNOWN', 'overall' => 'OK'];
    }
}
