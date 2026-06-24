<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\SyscohadaAccount;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalPostingService
{
    private const SCALE = 2;

    /**
     * Post a double-entry journal entry within an atomic transaction.
     *
     * @param array $entryData  Fields for JournalEntry (excluding lines)
     * @param array $lines      [['account_code' => '571200', 'debit' => '5000', 'credit' => '0', 'description' => '...'], ...]
     */
    public function post(array $entryData, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($entryData, $lines) {
            $totalDebit  = BigDecimal::zero();
            $totalCredit = BigDecimal::zero();

            $resolvedLines = [];

            foreach ($lines as $line) {
                $account = SyscohadaAccount::findByCode($line['account_code']);

                $debit  = BigDecimal::of($line['debit']  ?? '0')->toScale(self::SCALE, RoundingMode::HalfUp);
                $credit = BigDecimal::of($line['credit'] ?? '0')->toScale(self::SCALE, RoundingMode::HalfUp);

                $totalDebit  = $totalDebit->plus($debit)->toScale(self::SCALE, RoundingMode::HalfUp);
                $totalCredit = $totalCredit->plus($credit)->toScale(self::SCALE, RoundingMode::HalfUp);

                $resolvedLines[] = [
                    'syscohada_account_id' => $account->id,
                    'debit'                => (string) $debit,
                    'credit'               => (string) $credit,
                    'description'          => $line['description'] ?? null,
                ];
            }

            if (! $totalDebit->isEqualTo($totalCredit)) {
                throw ValidationException::withMessages([
                    'journal_lines' => [
                        "Ledger imbalance detected: Total Debits ({$totalDebit}) ≠ Total Credits ({$totalCredit}). Entry rejected.",
                    ],
                ]);
            }

            $entry = JournalEntry::create($entryData);

            foreach ($resolvedLines as $lineData) {
                $lineData['journal_entry_id'] = $entry->id;
                JournalLine::create($lineData);
            }

            return $entry->load('lines.account');
        });
    }
}
