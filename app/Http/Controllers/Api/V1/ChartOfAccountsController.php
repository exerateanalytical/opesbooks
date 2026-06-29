<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SyscohadaAccount;
use Illuminate\Http\Request;

class ChartOfAccountsController extends Controller
{
    public function index(Company $company)
    {
        // Standard (shared) accounts + this company's own custom accounts only.
        $accounts = \DB::table('syscohada_accounts')
            ->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', $company->id))
            ->orderBy('code')
            ->get();

        return response()->json($accounts);
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            // Code stays globally unique so the posting layer (findByCode) resolves
            // unambiguously; the new account is owned by this company.
            'code'        => 'required|string|max:10|unique:syscohada_accounts,code',
            'label'       => 'required|string|max:300',
            'class_digit' => 'required|integer|min:1|max:9',
        ]);

        $id = \DB::table('syscohada_accounts')->insertGetId([
            'company_id'  => $company->id,
            'code'        => $data['code'],
            'label'       => $data['label'],
            'class_digit' => $data['class_digit'],
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['id' => $id, ...$data], 201);
    }

    public function update(Request $request, Company $company, int $accountId)
    {
        $data = $request->validate([
            'label' => 'required|string|max:300',
        ]);

        $account = \DB::table('syscohada_accounts')->where('id', $accountId)->first();
        abort_if(! $account, 404);
        // A tenant may only edit their OWN custom accounts — never the shared
        // standard chart (company_id IS NULL) or another tenant's account.
        abort_if($account->company_id !== $company->id, 403, 'Standard accounts cannot be modified.');

        \DB::table('syscohada_accounts')->where('id', $accountId)->update([
            'label'      => $data['label'],
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Account updated.']);
    }
}
