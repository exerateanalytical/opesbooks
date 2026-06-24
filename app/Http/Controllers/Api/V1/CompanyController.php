<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Company::all());
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
        return response()->json($company);
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'niu'        => ['sometimes', 'string', 'max:20', Rule::unique('companies', 'niu')->ignore($company->id)],
            'rccm'       => ['sometimes', 'string', 'max:50', Rule::unique('companies', 'rccm')->ignore($company->id)],
            'tax_regime' => ['sometimes', Rule::in(['REEL', 'SIMPLIFIE', 'LIBERATOIRE'])],
            'tax_center' => 'sometimes|string|max:255',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string|max:500',
        ]);

        $company->update($data);

        return response()->json($company);
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json(null, 204);
    }
}
