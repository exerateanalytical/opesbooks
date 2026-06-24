<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Company;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function __construct(private BudgetService $svc) {}

    public function index(Company $company)
    {
        return response()->json(Budget::where('company_id', $company->id)->orderByDesc('fiscal_year')->get());
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'fiscal_year' => 'required|integer|min:2020|max:2099',
            'name'        => 'required|string|max:200',
            'lines'       => 'nullable|array',
            'lines.*.account_code'   => 'required|string|size:6',
            'lines.*.period_month'   => 'required|integer|min:1|max:12',
            'lines.*.budgeted_amount'=> 'required|numeric|min:0',
        ]);

        $budget = DB::transaction(function () use ($company, $data) {
            $budget = Budget::create([
                'company_id'  => $company->id,
                'fiscal_year' => $data['fiscal_year'],
                'name'        => $data['name'],
                'status'      => 'DRAFT',
            ]);

            foreach ($data['lines'] ?? [] as $line) {
                $budget->lines()->create([
                    'account_code'    => $line['account_code'],
                    'period_month'    => $line['period_month'],
                    'budgeted_amount' => $line['budgeted_amount'],
                ]);
            }

            return $budget;
        });

        return response()->json($budget->load('lines'), 201);
    }

    public function show(Company $company, Budget $budget)
    {
        abort_if($budget->company_id !== $company->id, 404);
        return response()->json($budget->load('lines'));
    }

    public function variance(Company $company, Budget $budget)
    {
        abort_if($budget->company_id !== $company->id, 404);
        return response()->json($this->svc->variance($budget));
    }

    public function destroy(Company $company, Budget $budget)
    {
        abort_if($budget->company_id !== $company->id, 404);
        $budget->lines()->delete();
        $budget->delete();
        return response()->json(['message' => 'Deleted.']);
    }
}
