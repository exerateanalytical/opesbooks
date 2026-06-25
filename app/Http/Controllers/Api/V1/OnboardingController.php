<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\User;
use App\Services\CameroonTaxEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    private function company(Request $request)
    {
        return $request->user()->company;
    }

    /** GET /api/v1/onboarding/status — completion + derived checklist. */
    public function status(Request $request): JsonResponse
    {
        $c = $this->company($request);
        $cid = $c?->id;

        $checklist = [
            'profile' => (bool) ($c && filled($c->niu) && filled($c->address)),
            'client'  => $cid ? Customer::where('company_id', $cid)->exists() : false,
            'invoice' => $cid ? CustomerInvoice::where('company_id', $cid)->exists() : false,
            'team'    => $cid ? User::where('company_id', $cid)->count() >= 2 : false,
            'report'  => (bool) cache()->get("onboarding_report_seen:{$cid}", false),
        ];

        return response()->json([
            'completed'          => (bool) ($c?->onboarding_completed),
            'step'               => $c?->onboarding_step ?? 1,
            'checklist'          => $checklist,
            'checklist_done'     => count(array_filter($checklist)),
            'checklist_total'    => count($checklist),
            'dismissed'          => (bool) ($c?->onboarding_checklist_dismissed),
        ]);
    }

    /** POST /api/v1/onboarding/profile */
    public function saveProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'niu'         => 'nullable|string|max:50',
            'rccm'        => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:500',
            'phone'       => 'nullable|string|max:30',
            'tax_regime'  => ['nullable', Rule::in(['REEL', 'SIMPLIFIE', 'LIBERATOIRE'])],
        ]);
        $this->company($request)->update(array_filter($data, fn ($v) => $v !== null));
        return response()->json(['ok' => true]);
    }

    /** POST /api/v1/onboarding/client */
    public function addClient(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'niu'   => 'nullable|string|max:50',
        ]);
        $data['company_id'] = $this->company($request)->id;
        $client = Customer::create($data);
        return response()->json(['ok' => true, 'client' => $client], 201);
    }

    /** POST /api/v1/onboarding/invoice */
    public function addInvoice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'description' => 'nullable|string|max:255',
            'amount_ht'   => 'required|numeric|min:0',
        ]);
        $tax = CameroonTaxEngine::compute((string) $data['amount_ht']);
        $invoice = CustomerInvoice::create([
            'company_id'     => $this->company($request)->id,
            'customer_id'    => $data['customer_id'],
            'invoice_number' => 'CLI-' . date('Y') . '-' . strtoupper(Str::random(6)),
            'invoice_date'   => now()->toDateString(),
            'due_date'       => now()->addDays(30)->toDateString(),
            'amount_ht'      => $tax['amount_ht'],
            'tva_amount'     => $tax['base_vat'],
            'cac_amount'     => $tax['cac'],
            'amount_ttc'     => $tax['amount_ttc'],
            'status'         => 'DRAFT',
            'notes'          => $data['description'] ?? null,
        ]);
        return response()->json(['ok' => true, 'invoice' => $invoice], 201);
    }

    /** POST /api/v1/onboarding/invite */
    public function invite(Request $request): JsonResponse
    {
        $data = $request->validate([
            'members'         => 'required|array|min:1',
            'members.*.email' => 'required|email|distinct|unique:users,email',
            'members.*.role'  => ['required', Rule::in(['ACCOUNTANT', 'CLERK'])],
        ]);
        $company = $this->company($request);
        $created = [];
        foreach ($data['members'] as $m) {
            $tempPassword = Str::random(12);
            $user = User::create([
                'company_id' => $company->id,
                'name'       => Str::before($m['email'], '@'),
                'email'      => $m['email'],
                'password'   => Hash::make($tempPassword),
                'role'       => $m['role'],
            ]);
            $user->companies()->attach($company->id, ['role' => $m['role'], 'is_default' => true]);
            $created[] = $m['email'];
        }
        // Note: invited users set their password via the forgot-password flow.
        return response()->json(['ok' => true, 'invited' => $created], 201);
    }

    /** POST /api/v1/onboarding/complete */
    public function complete(Request $request): JsonResponse
    {
        $this->company($request)->update([
            'onboarding_completed'    => true,
            'onboarding_completed_at' => now(),
            'onboarding_step'         => 5,
        ]);
        return response()->json(['ok' => true]);
    }

    /** POST /api/v1/onboarding/dismiss-checklist */
    public function dismissChecklist(Request $request): JsonResponse
    {
        $this->company($request)->update(['onboarding_checklist_dismissed' => true]);
        return response()->json(['ok' => true]);
    }
}
