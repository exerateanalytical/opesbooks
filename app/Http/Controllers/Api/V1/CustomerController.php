<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request, Company $company): JsonResponse
    {
        $customers = Customer::where('company_id', $company->id)
            ->when($request->boolean('active_only', true), fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();
        return response()->json($customers);
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
            'credit_limit_xaf'   => 'nullable|numeric|min:0',
        ]);
        $customer = Customer::create(['company_id' => $company->id, ...$data]);
        return response()->json($customer, 201);
    }

    public function show(Company $company, Customer $customer): JsonResponse
    {
        abort_if($customer->company_id !== $company->id, 404);
        return response()->json($customer->load('invoices'));
    }

    public function update(Request $request, Company $company, Customer $customer): JsonResponse
    {
        abort_if($customer->company_id !== $company->id, 404);
        $data = $request->validate([
            'name'               => 'sometimes|string|max:255',
            'niu'                => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'nullable|string|max:500',
            'payment_terms_days' => 'nullable|integer|min:0|max:365',
            'credit_limit_xaf'   => 'nullable|numeric|min:0',
            'is_active'          => 'sometimes|boolean',
        ]);
        $customer->update($data);
        return response()->json($customer);
    }

    public function destroy(Company $company, Customer $customer): JsonResponse
    {
        abort_if($customer->company_id !== $company->id, 404);
        $customer->delete();
        return response()->json(['message' => 'Customer deleted.']);
    }
}
