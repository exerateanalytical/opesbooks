<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\JournalPostingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\Csv\Reader;

class BankStatementController extends Controller
{
    public function __construct(private JournalPostingService $poster) {}

    /**
     * POST /api/v1/companies/{company}/bank-statement/import
     *
     * Accepts a CSV bank statement export (Afriland, Ecobank, SGBC, etc.)
     * with a user-defined column mapping and auto-posts entries to account 521100.
     *
     * Column mapping keys: date_col, reference_col, debit_col, credit_col, memo_col
     * (0-based column indices matching the uploaded CSV structure)
     */
    public function import(Request $request, Company $company): JsonResponse
    {
        $request->validate([
            'csv_file'      => 'required|file|mimes:csv,txt|max:5120',
            'date_col'      => 'required|integer|min:0',
            'reference_col' => 'required|integer|min:0',
            'debit_col'     => 'required|integer|min:0',
            'credit_col'    => 'required|integer|min:0',
            'memo_col'      => 'nullable|integer|min:0',
            'skip_rows'     => 'nullable|integer|min:0|max:10',
            'delimiter'     => 'nullable|string|max:1',
        ]);

        $path      = $request->file('csv_file')->getRealPath();
        $delimiter = $request->input('delimiter', ',');

        $csv = Reader::createFromPath($path, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(null);

        $skipRows  = (int) $request->input('skip_rows', 1);
        $records   = array_values(iterator_to_array($csv->getRecords()));
        $dataRows  = array_slice($records, $skipRows);

        $dateCol  = (int) $request->input('date_col');
        $refCol   = (int) $request->input('reference_col');
        $debitCol = (int) $request->input('debit_col');
        $creditCol = (int) $request->input('credit_col');
        $memoCol  = $request->input('memo_col') !== null ? (int) $request->input('memo_col') : null;

        $posted  = [];
        $skipped = [];

        foreach ($dataRows as $index => $row) {
            $rawDate   = trim($row[$dateCol]  ?? '');
            $rawRef    = trim($row[$refCol]   ?? '');
            $rawDebit  = $this->parseAmount($row[$debitCol]  ?? '0');
            $rawCredit = $this->parseAmount($row[$creditCol] ?? '0');
            $memo      = $memoCol !== null ? trim($row[$memoCol] ?? '') : "Bank import row {$index}";

            if (empty($rawDate) || (! $rawDebit && ! $rawCredit)) {
                $skipped[] = "Row {$index}: empty date or zero amount";
                continue;
            }

            try {
                $postingDate = date('Y-m-d', strtotime($rawDate));
            } catch (\Throwable) {
                $skipped[] = "Row {$index}: unparseable date '{$rawDate}'";
                continue;
            }

            $referenceId = 'BANK-' . Str::slug($rawRef ?: "row-{$index}") . '-' . Str::random(6);

            try {
                if ($rawDebit > 0) {
                    // Bank debit = money leaving = credit 521100, debit expense/supplier
                    $entry = $this->poster->post([
                        'company_id'         => $company->id,
                        'posting_date'       => $postingDate,
                        'reference_id'       => $referenceId,
                        'source_pipeline'    => 'BANK_CSV',
                        'posting_type'       => 'STANDARD',
                        'transaction_status' => 'SUCCESSFUL',
                        'memo'               => $memo,
                    ], [
                        ['account_code' => '401100', 'debit' => (string) $rawDebit, 'credit' => '0.00', 'description' => "Bank debit: {$memo}"],
                        ['account_code' => '521100', 'debit' => '0.00', 'credit' => (string) $rawDebit, 'description' => "Sortie bancaire"],
                    ]);
                } else {
                    // Bank credit = money received = debit 521100, credit revenue
                    $entry = $this->poster->post([
                        'company_id'         => $company->id,
                        'posting_date'       => $postingDate,
                        'reference_id'       => $referenceId,
                        'source_pipeline'    => 'BANK_CSV',
                        'posting_type'       => 'STANDARD',
                        'transaction_status' => 'SUCCESSFUL',
                        'memo'               => $memo,
                    ], [
                        ['account_code' => '521100', 'debit' => (string) $rawCredit, 'credit' => '0.00', 'description' => "Entrée bancaire"],
                        ['account_code' => '701100', 'debit' => '0.00', 'credit' => (string) $rawCredit, 'description' => "Recette bancaire: {$memo}"],
                    ]);
                }

                $posted[] = ['reference_id' => $entry->reference_id, 'row' => $index];
            } catch (\Throwable $e) {
                $skipped[] = "Row {$index}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'message'       => 'Bank statement import complete.',
            'total_rows'    => count($dataRows),
            'posted_count'  => count($posted),
            'skipped_count' => count($skipped),
            'posted'        => $posted,
            'skipped'       => $skipped,
        ], 201);
    }

    private function parseAmount(string $raw): float
    {
        // Strip spaces, thousands separators (dot or comma), then parse
        $cleaned = preg_replace('/[^\d,.\-]/', '', $raw);
        $cleaned = str_replace(',', '.', $cleaned); // Handle European format
        // If more than one dot, assume thousands separator
        if (substr_count($cleaned, '.') > 1) {
            $cleaned = str_replace('.', '', $cleaned);
        }
        return (float) $cleaned;
    }
}
