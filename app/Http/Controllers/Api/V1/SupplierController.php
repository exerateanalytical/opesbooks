<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request, Company $company): JsonResponse
    {
        $suppliers = Supplier::where('company_id', $company->id)
            ->when($request->boolean('active_only', true), fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();
        return response()->json($suppliers);
    }

    public function store(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'niu'                => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'nullable|string|max:500',
            'payment_terms_days' => 'nullable|integer|min:0|max:365',
        ]);
        $supplier = Supplier::create(['company_id' => $company->id, ...$data]);
        return response()->json($supplier, 201);
    }

    public function update(Request $request, Company $company, Supplier $supplier): JsonResponse
    {
        abort_if($supplier->company_id !== $company->id, 404);
        $data = $request->validate([
            'name'               => 'sometimes|string|max:255',
            'niu'                => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'nullable|string|max:500',
            'payment_terms_days' => 'nullable|integer|min:0|max:365',
            'is_active'          => 'sometimes|boolean',
        ]);
        $supplier->update($data);
        return response()->json($supplier);
    }

    public function destroy(Company $company, Supplier $supplier): JsonResponse
    {
        abort_if($supplier->company_id !== $company->id, 404);
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted.']);
    }
}
