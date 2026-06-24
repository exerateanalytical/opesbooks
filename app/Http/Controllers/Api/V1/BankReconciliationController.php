<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BankReconciliationSession;
use App\Models\BankStatementLine;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function index(Company $company)
    {
        return response()->json(
            BankReconciliationSession::where('company_id', $company->id)
                ->orderByDesc('statement_date')
                ->get()
        );
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'bank_account_code'         => 'required|string',
            'statement_date'            => 'required|date',
            'statement_balance'         => 'required|numeric',
            'lines'                     => 'nullable|array',
            'lines.*.transaction_date'  => 'required|date',
            'lines.*.description'       => 'nullable|string|max:500',
            'lines.*.amount'            => 'required|numeric|min:0',
            'lines.*.is_credit'         => 'required|boolean',
        ]);

        $session = DB::transaction(function () use ($company, $data) {
            $session = BankReconciliationSession::create([
                'company_id'        => $company->id,
                'bank_account_code' => $data['bank_account_code'],
                'statement_date'    => $data['statement_date'],
                'statement_balance' => $data['statement_balance'],
                'book_balance'      => 0,
                'difference'        => 0,
                'is_reconciled'     => false,
            ]);

            foreach ($data['lines'] ?? [] as $line) {
                BankStatementLine::create([
                    'bank_reconciliation_session_id' => $session->id,
                    'transaction_date' => $line['transaction_date'],
                    'description'      => $line['description'] ?? null,
                    'amount'           => $line['amount'],
                    'is_credit'        => $line['is_credit'],
                    'is_matched'       => false,
                ]);
            }

            return $session;
        });

        return response()->json($session->load('lines'), 201);
    }

    public function show(Company $company, BankReconciliationSession $session)
    {
        abort_if($session->company_id !== $company->id, 404);

        $lines = $session->lines()->get();

        $gl = DB::select("
            SELECT je.posting_date, je.memo,
                   jl.debit, jl.credit, jl.id AS journal_line_id
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND sa.code = ?
              AND je.posting_date <= ?
              AND je.deleted_at IS NULL
            ORDER BY je.posting_date
        ", [$company->id, $session->bank_account_code, $session->statement_date]);

        return response()->json([
            'session'      => $session,
            'lines'        => $lines,
            'gl_movements' => $gl,
        ]);
    }

    public function matchLine(Request $request, Company $company, BankReconciliationSession $session, BankStatementLine $line)
    {
        abort_if($session->company_id !== $company->id, 404);
        abort_if($line->bank_reconciliation_session_id !== $session->id, 404);

        $data = $request->validate([
            'journal_line_id' => 'nullable|exists:journal_lines,id',
        ]);

        $line->update([
            'is_matched'      => true,
            'journal_line_id' => $data['journal_line_id'] ?? null,
        ]);

        return response()->json($line);
    }

    public function close(Company $company, BankReconciliationSession $session)
    {
        abort_if($session->company_id !== $company->id, 404);

        // Compute book balance from GL
        $glBalance = DB::selectOne("
            SELECT COALESCE(SUM(jl.credit) - SUM(jl.debit), 0) AS balance
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND sa.code = ?
              AND je.posting_date <= ?
              AND je.deleted_at IS NULL
        ", [$company->id, $session->bank_account_code, $session->statement_date]);

        $bookBalance = (float)($glBalance->balance ?? 0);
        $difference  = $session->statement_balance - $bookBalance;
        $unmatched   = $session->lines()->where('is_matched', false)->count();

        $session->update([
            'book_balance'   => $bookBalance,
            'difference'     => $difference,
            'is_reconciled'  => $unmatched === 0 && abs($difference) < 1,
            'reconciled_at'  => now(),
        ]);

        return response()->json($session);
    }
}
