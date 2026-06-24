<?php

namespace App\Services;

use App\Models\Company;
use App\Models\JournalEntry;
use Illuminate\Support\Str;

class MomoIngestionService
{
    private JournalPostingService $poster;

    public function __construct(JournalPostingService $poster)
    {
        $this->poster = $poster;
    }

    public function ingest(Company $company, array $payload): JournalEntry
    {
        $operator      = strtoupper($payload['operator'] ?? 'MTN');
        $amountStr     = (string) ($payload['amount'] ?? '0');
        $transactionId = $payload['transaction_id'] ?? Str::uuid()->toString();
        $message       = $payload['message'] ?? '';
        $postingDate   = $payload['date'] ?? now()->toDateString();

        $walletAccount = $operator === 'ORANGE' ? '571300' : '571200';
        $pipeline      = $operator === 'ORANGE' ? 'AUTOMATED_ORANGE' : 'AUTOMATED_MOMO';

        if ($this->isRevenue($message)) {
            return $this->postRevenue(
                $company, $amountStr, $walletAccount, $pipeline, $transactionId, $postingDate, $message
            );
        }

        return $this->postExpense(
            $company, $amountStr, $walletAccount, $pipeline, $transactionId, $postingDate, $message
        );
    }

    private function isRevenue(string $message): bool
    {
        return (bool) preg_match('/Transfert[_ ]re[çc]u|paiement[_ ]re[çc]u|payment[_ ]received/i', $message);
    }

    private function postRevenue(
        Company $company,
        string $amount,
        string $walletAccount,
        string $pipeline,
        string $transactionId,
        string $postingDate,
        string $message
    ): JournalEntry {
        $tax = CameroonTaxEngine::reverseFromTtc($amount);

        $lines = [
            // Debit wallet — full TTC amount received
            ['account_code' => $walletAccount, 'debit' => $amount, 'credit' => '0.00', 'description' => "Receipt: {$message}"],
            // Credit revenue HT
            ['account_code' => '701100', 'debit' => '0.00', 'credit' => $tax['amount_ht'], 'description' => 'Revenue HT'],
            // Credit TVA collectée
            ['account_code' => '443100', 'debit' => '0.00', 'credit' => $tax['base_vat'], 'description' => 'TVA 17.5% collectée'],
            // Credit CAC
            ['account_code' => '448600', 'debit' => '0.00', 'credit' => $tax['cac'], 'description' => 'CAC 10% sur TVA'],
        ];

        return $this->poster->post([
            'company_id'    => $company->id,
            'posting_date'  => $postingDate,
            'reference_id'  => $transactionId,
            'source_pipeline' => $pipeline,
            'memo'          => $message,
            'status'        => 'POSTED',
        ], $lines);
    }

    private function postExpense(
        Company $company,
        string $amount,
        string $walletAccount,
        string $pipeline,
        string $transactionId,
        string $postingDate,
        string $message
    ): JournalEntry {
        $lines = [
            // Debit expense account
            ['account_code' => '618100', 'debit' => $amount, 'credit' => '0.00', 'description' => "Expense: {$message}"],
            // Credit wallet
            ['account_code' => $walletAccount, 'debit' => '0.00', 'credit' => $amount, 'description' => 'Payment from wallet'],
        ];

        return $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => $postingDate,
            'reference_id'    => $transactionId,
            'source_pipeline' => $pipeline,
            'memo'            => $message,
            'status'          => 'POSTED',
        ], $lines);
    }
}
