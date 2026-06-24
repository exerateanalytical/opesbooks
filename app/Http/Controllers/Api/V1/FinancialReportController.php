<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\FinancialStatementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function __construct(private FinancialStatementService $svc) {}

    public function profitAndLoss(Request $request, Company $company): JsonResponse
    {
        $request->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        return response()->json($this->svc->profitAndLoss($company, $request->from, $request->to));
    }

    public function balanceSheet(Request $request, Company $company): JsonResponse
    {
        $request->validate(['as_of' => 'required|date']);
        return response()->json($this->svc->balanceSheet($company, $request->as_of));
    }

    public function cashFlow(Request $request, Company $company): JsonResponse
    {
        $request->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        return response()->json($this->svc->cashFlow($company, $request->from, $request->to));
    }

    public function agedReceivables(Company $company): JsonResponse
    {
        return response()->json($this->svc->agedReceivables($company));
    }
}
