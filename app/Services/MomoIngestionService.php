<?php

namespace App\Services;

use App\Models\Company;
use App\Models\JournalEntry;
use Illuminate\Support\Str;

class MomoIngestionService
{
    public function __construct(private JournalPostingService $poster) {}

    public function ingest(Company $company, array $payload): JournalEntry
    {
        $operator      = strtoupper($payload['operator'] ?? 'MTN');
        $amountStr     = (string) ($payload['amount'] ?? '0');
        $transactionId = $payload['transaction_id'] ?? Str::uuid()->toString();
        $message       = $payload['message'] ?? '';
        $postingDate   = $payload['date'] ?? now()->toDateString();

        // Route to correct sub-ledger: primary wallet accounts
        $walletAccount = $operator === 'ORANGE' ? '571301' : '571201';
        $pipeline      = $operator === 'ORANGE' ? 'ORANGE_AUTO' : 'MOMO_AUTO';

        if ($this->isRevenue($message)) {
            return $this->postRevenue($company, $amountStr, $walletAccount, $pipeline, $transactionId, $postingDate, $message);
        }

        return $this->postExpense($company, $amountStr, $walletAccount, $pipeline, $transactionId, $postingDate, $message);
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
            ['account_code' => $walletAccount, 'debit' => $amount,            'credit' => '0.00',            'description' => "Receipt: {$message}"],
            ['account_code' => '701100',        'debit' => '0.00',            'credit' => $tax['amount_ht'], 'description' => 'Revenue HT'],
            ['account_code' => '443100',        'debit' => '0.00',            'credit' => $tax['base_vat'],  'description' => 'TVA 17.5% collectée'],
            ['account_code' => '448600',        'debit' => '0.00',            'credit' => $tax['cac'],       'description' => 'CAC 10% sur TVA'],
        ];

        return $this->poster->post([
            'company_id'         => $company->id,
            'posting_date'       => $postingDate,
            'posting_type'       => 'STANDARD',
            'reference_id'       => $transactionId,
            'source_pipeline'    => $pipeline,
            'transaction_status' => 'SUCCESSFUL',
            'memo'               => $message,
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
            ['account_code' => '618100',      'debit' => $amount,  'credit' => '0.00', 'description' => "Expense: {$message}"],
            ['account_code' => $walletAccount, 'debit' => '0.00', 'credit' => $amount, 'description' => 'Payment from wallet'],
        ];

        return $this->poster->post([
            'company_id'         => $company->id,
            'posting_date'       => $postingDate,
            'posting_type'       => 'STANDARD',
            'reference_id'       => $transactionId,
            'source_pipeline'    => $pipeline,
            'transaction_status' => 'SUCCESSFUL',
            'memo'               => $message,
        ], $lines);
    }
}
