<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /** Abort unless the caller is a member of (or actively in) this company. */
    private function ensureAccess(Company $company): void
    {
        $user = auth()->user();
        abort_unless(
            $user && ((int) $user->company_id === (int) $company->id || $user->belongsToCompany($company->id)),
            403,
            'You do not have access to this company.'
        );
    }

    public function index(Request $request): JsonResponse
    {
        // Only the companies this user belongs to — never the whole platform.
        $ids = $request->user()->companies()->pluck('companies.id')
            ->push($request->user()->company_id)->filter()->unique();

        return response()->json(Company::whereIn('id', $ids)->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'niu'        => 'required|string|max:20|unique:companies,niu',
            'rccm'       => 'required|string|max:50|unique:companies,rccm',
            'tax_regime' => ['required', Rule::in(['REEL', 'SIMPLIFIE', 'LIBERATOIRE'])],
            'tax_center' => 'required|string|max:255',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string|max:500',
        ]);

        $company = Company::create($data);

        return response()->json($company, 201);
    }

    public function show(Company $company): JsonResponse
    {
        $this->ensureAccess($company);
        $data = $company->toArray();
        $data['logo_url'] = $company->logo_path ? Storage::url($company->logo_path) : null;
        return response()->json($data);
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $this->ensureAccess($company);
        $data = $request->validate([
            'name'                => 'sometimes|string|max:255',
            'niu'                 => ['sometimes', 'string', 'max:20', Rule::unique('companies', 'niu')->ignore($company->id)],
            'rccm'                => ['sometimes', 'string', 'max:50', Rule::unique('companies', 'rccm')->ignore($company->id)],
            'tax_regime'          => ['sometimes', Rule::in(['REEL', 'SIMPLIFIE', 'LIBERATOIRE'])],
            'tax_center'          => 'sometimes|string|max:255',
            'phone'               => 'nullable|string|max:30',
            'email'               => 'nullable|email|max:255',
            'address'             => 'nullable|string|max:500',
            'letterhead_tagline'  => 'nullable|string|max:255',
            'letterhead_website'  => 'nullable|string|max:255',
            'bank_name'           => 'nullable|string|max:255',
            'bank_account'        => 'nullable|string|max:100',
            'bank_rib'            => 'nullable|string|max:100',
            'invoice_footer_note' => 'nullable|string|max:500',
        ]);

        $company->update($data);

        $result = $company->fresh()->toArray();
        $result['logo_url'] = $company->logo_path ? Storage::url($company->logo_path) : null;

        return response()->json($result);
    }

    public function uploadLogo(Request $request, Company $company): JsonResponse
    {
        $request->validate(['logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048']);

        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            Storage::disk('public')->delete($company->logo_path);
        }

        $path = $request->file('logo')->store("logos/{$company->id}", 'public');
        $company->update(['logo_path' => $path]);

        return response()->json(['logo_url' => Storage::url($path)]);
    }

    public function destroy(Company $company): JsonResponse
    {
        $this->ensureAccess($company);
        $company->delete();

        return response()->json(null, 204);
    }
}
