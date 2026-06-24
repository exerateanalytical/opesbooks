<?php

namespace App\Services;

use App\Models\RecurringTransaction;
use App\Models\SyscohadaAccount;
use Illuminate\Support\Str;

class RecurringTransactionService
{
    public function __construct(private JournalPostingService $poster) {}

    /**
     * Process all due recurring transactions for all companies.
     * Called by the scheduler daily.
     */
    public function processDue(): int
    {
        $processed = 0;
        $due = RecurringTransaction::where('is_active', true)
            ->where('next_run_date', '<=', now()->toDateString())
            ->with('company')
            ->get();

        foreach ($due as $rt) {
            try {
                $this->runOne($rt);
                $rt->advanceNextRun();
                $processed++;
            } catch (\Throwable $e) {
                \Log::error("RecurringTransaction #{$rt->id} failed: " . $e->getMessage());
            }
        }
        return $processed;
    }

    private function runOne(RecurringTransaction $rt): void
    {
        $debitAccount  = SyscohadaAccount::where('code', $rt->debit_account)->firstOrFail();
        $creditAccount = SyscohadaAccount::where('code', $rt->credit_account)->firstOrFail();

        $this->poster->post([
            'company_id'      => $rt->company_id,
            'posting_date'    => $rt->next_run_date->toDateString(),
            'reference_id'    => 'REC-' . strtoupper(Str::random(10)),
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "[Récurrent] {$rt->name}: {$rt->memo}",
            'posting_type'    => 'STANDARD',
        ], [
            ['account_code' => $rt->debit_account,  'debit'  => $rt->amount_xaf, 'credit' => 0],
            ['account_code' => $rt->credit_account, 'debit'  => 0, 'credit' => $rt->amount_xaf],
        ]);
    }
}
