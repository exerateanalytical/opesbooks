<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\RecurringTransaction;
use App\Services\RecurringTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecurringTransactionController extends Controller
{
    public function __construct(private RecurringTransactionService $svc) {}

    public function index(Company $company): JsonResponse
    {
        return response()->json(
            RecurringTransaction::where('company_id', $company->id)->orderBy('next_run_date')->get()
        );
    }

    public function store(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'debit_account'  => 'required|string|exists:syscohada_accounts,code',
            'credit_account' => 'required|string|exists:syscohada_accounts,code',
            'amount_xaf'     => 'required|numeric|min:1',
            'memo'           => 'required|string|max:500',
            'frequency'      => ['required', Rule::in(['DAILY','WEEKLY','MONTHLY','QUARTERLY','YEARLY'])],
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after:start_date',
        ]);

        $rt = RecurringTransaction::create([
            'company_id'    => $company->id,
            'next_run_date' => $data['start_date'],
            ...$data,
        ]);

        return response()->json($rt, 201);
    }

    public function update(Request $request, Company $company, RecurringTransaction $recurring): JsonResponse
    {
        abort_if($recurring->company_id !== $company->id, 404);
        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'amount_xaf' => 'sometimes|numeric|min:1',
            'memo'       => 'sometimes|string|max:500',
            'end_date'   => 'nullable|date',
            'is_active'  => 'sometimes|boolean',
        ]);
        $recurring->update($data);
        return response()->json($recurring);
    }

    public function destroy(Company $company, RecurringTransaction $recurring): JsonResponse
    {
        abort_if($recurring->company_id !== $company->id, 404);
        $recurring->delete();
        return response()->json(['message' => 'Recurring transaction deleted.']);
    }

    public function runNow(Company $company): JsonResponse
    {
        $count = $this->svc->processDue();
        return response()->json(['message' => "Processed {$count} recurring transaction(s)."]);
    }
}
