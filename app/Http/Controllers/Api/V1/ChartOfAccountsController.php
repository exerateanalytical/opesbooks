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
        // Return all accounts (global + company-specific)
        $accounts = \DB::table('syscohada_accounts')
            ->orderBy('code')
            ->get();

        return response()->json($accounts);
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:10|unique:syscohada_accounts,code',
            'label'       => 'required|string|max:300',
            'class_digit' => 'required|integer|min:1|max:9',
        ]);

        $account = \DB::table('syscohada_accounts')->insertGetId([
            'code'        => $data['code'],
            'label'       => $data['label'],
            'class_digit' => $data['class_digit'],
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['id' => $account, ...$data], 201);
    }

    public function update(Request $request, Company $company, int $accountId)
    {
        $data = $request->validate([
            'label' => 'required|string|max:300',
        ]);

        \DB::table('syscohada_accounts')->where('id', $accountId)->update([
            'label'      => $data['label'],
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Account updated.']);
    }
}
