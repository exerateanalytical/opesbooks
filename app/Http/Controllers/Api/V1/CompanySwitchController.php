<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompanySwitchController extends Controller
{
    /** GET /api/v1/companies/mine — companies this user can access. */
    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();

        $companies = $user->companies()->get()->map(function (Company $c) use ($user) {
            return [
                'id'        => $c->id,
                'name'      => $c->name,
                'role'      => $c->pivot->role,
                'is_active' => $c->id === $user->company_id,
                'logo_url'  => $c->logo_path ? Storage::url($c->logo_path) : null,
            ];
        })->values();

        return response()->json($companies);
    }

    /** POST /api/v1/companies/switch — set the active company. */
    public function switch(Request $request): JsonResponse
    {
        $data = $request->validate(['company_id' => 'required|integer']);
        $user = $request->user();

        if (! $user->belongsToCompany($data['company_id'])) {
            abort(403, 'You do not have access to that company.');
        }

        $role = $user->roleInCompany($data['company_id']) ?? $user->role;

        // users.company_id is the active-company pointer used by every controller.
        $user->forceFill([
            'company_id' => $data['company_id'],
            'role'       => $role,
        ])->save();

        $company = Company::find($data['company_id']);
        $companyData = $company->toArray();
        if ($company->logo_path) {
            $companyData['logo_url'] = Storage::url($company->logo_path);
        }

        return response()->json([
            'message' => 'Active company switched.',
            'company' => $companyData,
            'role'    => $role,
        ]);
    }

    /** POST /api/v1/companies/additional — create a new company for the current user. */
    public function createAdditional(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'niu'         => 'required|string|unique:companies,niu',
            'rccm'        => 'required|string|unique:companies,rccm',
            'tax_regime'  => ['required', Rule::in(['REEL', 'SIMPLIFIE', 'LIBERATOIRE'])],
            'tax_center'  => 'required|string|max:100',
        ]);

        $user = $request->user();

        $company = Company::create([
            'name'                    => $data['name'],
            'niu'                     => strtoupper($data['niu']),
            'rccm'                    => strtoupper($data['rccm']),
            'tax_regime'              => $data['tax_regime'],
            'tax_center'              => $data['tax_center'],
            'vat_prorata_coefficient' => 100.00,
            'subscription_status'     => 'ACTIVE',
        ]);

        // Owner membership for the current user.
        $user->companies()->attach($company->id, ['role' => 'OWNER', 'is_default' => false]);

        return response()->json([
            'message' => 'Company created.',
            'company' => $company,
        ], 201);
    }
}
