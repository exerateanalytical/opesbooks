<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\JournalPostingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Self-service CSV import wizard: downloadable templates + dry-run validation
 * preview + commit, for customers, suppliers and journal entries.
 */
class DataImportController extends Controller
{
    /** Column spec per importable type. */
    private function spec(string $type): array
    {
        return match ($type) {
            'customers' => [
                'headers'  => ['name', 'niu', 'email', 'phone', 'address', 'payment_terms_days', 'credit_limit_xaf'],
                'required' => ['name'],
                'example'  => ['SARL Exemple', 'M012345678901X', 'contact@exemple.cm', '+237699000000', 'Akwa, Douala', '30', '5000000'],
            ],
            'suppliers' => [
                'headers'  => ['name', 'niu', 'email', 'phone', 'address', 'payment_terms_days'],
                'required' => ['name'],
                'example'  => ['Fournisseur SA', 'M012345678901Y', 'achats@fournisseur.cm', '+237677000000', 'Bonanjo, Douala', '45'],
            ],
            'journal' => [
                'headers'  => ['posting_date', 'reference_id', 'memo', 'account_code', 'debit', 'credit', 'description'],
                'required' => ['posting_date', 'reference_id', 'memo', 'account_code'],
                'example'  => ['2026-01-15', 'OD-2026-001', 'Achat fournitures', '6055000', '50000', '0', 'Fournitures bureau'],
            ],
            default => abort(404, 'Unknown import type.'),
        };
    }

    /** GET /companies/{company}/import/template/{type} */
    public function template(Company $company, string $type): StreamedResponse
    {
        $spec = $this->spec($type);
        $filename = "opesbooks_template_{$type}.csv";

        return response()->stream(function () use ($spec) {
            $out = fopen('php://output', 'w');
            fprintf($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($out, $spec['headers']);
            fputcsv($out, $spec['example']);
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /** POST /companies/{company}/import/preview  (dry run — nothing is saved) */
    public function preview(Request $request, Company $company): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:customers,suppliers,journal',
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $type = $request->input('type');
        $rows = $this->parse($request->file('file'), $this->spec($type)['headers']);
        $report = $this->validateRows($type, $rows);

        return response()->json($report);
    }

    /** POST /companies/{company}/import/commit  (imports valid rows) */
    public function commit(Request $request, Company $company): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:customers,suppliers,journal',
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $type   = $request->input('type');
        $rows   = $this->parse($request->file('file'), $this->spec($type)['headers']);
        $report = $this->validateRows($type, $rows);

        if ($report['error_count'] > 0) {
            return response()->json([
                'message' => 'Le fichier contient des erreurs. Corrigez-les avant l\'import.',
                'report'  => $report,
            ], 422);
        }

        $imported = $type === 'journal'
            ? $this->importJournal($company, $rows)
            : $this->importContacts($company, $type, $rows);

        return response()->json([
            'message'  => "Import terminé : {$imported} enregistrement(s).",
            'imported' => $imported,
        ]);
    }

    /** Parse an uploaded CSV into associative rows keyed by the spec headers. */
    private function parse($file, array $headers): array
    {
        $rows   = [];
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) return $rows;

        $first = true;
        $line  = 1;
        while (($cols = fgetcsv($handle)) !== false) {
            if ($first) {
                // Strip BOM from the very first cell.
                if (isset($cols[0])) $cols[0] = preg_replace('/^\xEF\xBB\xBF/', '', $cols[0]);
                $first = false;
                $line++;
                // Skip the header row if it matches our headers.
                $norm = array_map(fn ($c) => strtolower(trim((string) $c)), $cols);
                if (array_slice($norm, 0, count($headers)) === array_map('strtolower', $headers)) {
                    continue;
                }
            }
            if (count(array_filter($cols, fn ($c) => trim((string) $c) !== '')) === 0) { $line++; continue; }

            $row = [];
            foreach ($headers as $i => $h) {
                $row[$h] = isset($cols[$i]) ? trim((string) $cols[$i]) : '';
            }
            $row['_line'] = $line;
            $rows[] = $row;
            $line++;
        }
        fclose($handle);
        return $rows;
    }

    /** Validate parsed rows; return a preview report (no persistence). */
    private function validateRows(string $type, array $rows): array
    {
        $spec     = $this->spec($type);
        $preview  = [];
        $errorCnt = 0;

        // For journal, accumulate per-reference balance.
        $balances = [];

        foreach ($rows as $row) {
            $errors = [];
            foreach ($spec['required'] as $req) {
                if (($row[$req] ?? '') === '') $errors[] = "« {$req} » est requis";
            }

            if ($type === 'journal') {
                if (($row['posting_date'] ?? '') !== '' && ! \DateTime::createFromFormat('Y-m-d', $row['posting_date'])) {
                    $errors[] = 'date invalide (format AAAA-MM-JJ)';
                }
                if (($row['account_code'] ?? '') !== '' && ! \App\Models\SyscohadaAccount::where('code', $row['account_code'])->where('is_active', true)->exists()) {
                    $errors[] = "compte SYSCOHADA « {$row['account_code']} » introuvable";
                }
                $d = is_numeric($row['debit'] ?? '') ? (float) $row['debit'] : 0;
                $c = is_numeric($row['credit'] ?? '') ? (float) $row['credit'] : 0;
                if (($row['debit'] ?? '') !== '' && ! is_numeric($row['debit']))  $errors[] = 'débit non numérique';
                if (($row['credit'] ?? '') !== '' && ! is_numeric($row['credit'])) $errors[] = 'crédit non numérique';
                if ($d > 0 && $c > 0) $errors[] = 'une ligne ne peut être à la fois débit et crédit';
                if (($row['reference_id'] ?? '') !== '') {
                    $balances[$row['reference_id']] = ($balances[$row['reference_id']] ?? 0) + $d - $c;
                }
            } else {
                if (($row['email'] ?? '') !== '' && ! filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'email invalide';
                }
            }

            if ($errors) $errorCnt += 1;
            $preview[] = ['line' => $row['_line'], 'data' => collect($row)->except('_line')->all(), 'errors' => $errors];
        }

        // Journal: flag unbalanced references.
        if ($type === 'journal') {
            foreach ($balances as $ref => $bal) {
                if (round($bal, 2) !== 0.0) {
                    foreach ($preview as &$p) {
                        if (($p['data']['reference_id'] ?? null) === $ref && ! in_array("écriture « {$ref} » déséquilibrée", $p['errors'])) {
                            $p['errors'][] = "écriture « {$ref} » déséquilibrée (débit ≠ crédit)";
                            $errorCnt++;
                        }
                    }
                    unset($p);
                }
            }
        }

        return [
            'type'        => $type,
            'headers'     => $spec['headers'],
            'total'       => count($preview),
            'valid_count' => count($preview) - $errorCnt,
            'error_count' => $errorCnt,
            'rows'        => array_slice($preview, 0, 500),
        ];
    }

    private function importContacts(Company $company, string $type, array $rows): int
    {
        $model = $type === 'customers' ? Customer::class : Supplier::class;
        $n = 0;
        foreach ($rows as $row) {
            $attrs = [
                'company_id'         => $company->id,
                'name'               => $row['name'],
                'niu'                => $row['niu'] ?: null,
                'email'              => $row['email'] ?: null,
                'phone'              => $row['phone'] ?: null,
                'address'            => $row['address'] ?: null,
                'payment_terms_days' => is_numeric($row['payment_terms_days'] ?? '') ? (int) $row['payment_terms_days'] : 0,
                'is_active'          => true,
            ];
            if ($type === 'customers') {
                $attrs['credit_limit_xaf'] = is_numeric($row['credit_limit_xaf'] ?? '') ? (int) $row['credit_limit_xaf'] : 0;
            }
            $model::create($attrs);
            $n++;
        }
        return $n;
    }

    private function importJournal(Company $company, array $rows): int
    {
        // Group rows by reference into balanced double-entry entries.
        $groups = [];
        foreach ($rows as $row) {
            $groups[$row['reference_id']]['meta'] ??= [
                'posting_date' => $row['posting_date'],
                'memo'         => $row['memo'],
            ];
            $groups[$row['reference_id']]['lines'][] = [
                'account_code' => $row['account_code'],
                'debit'        => is_numeric($row['debit'] ?? '') ? $row['debit'] : '0',
                'credit'       => is_numeric($row['credit'] ?? '') ? $row['credit'] : '0',
                'description'  => $row['description'] ?: null,
            ];
        }

        $poster = app(JournalPostingService::class);
        $n = 0;
        foreach ($groups as $ref => $g) {
            $poster->post([
                'company_id'         => $company->id,
                'user_id'            => optional(request()->user())->id,
                'posting_date'       => $g['meta']['posting_date'],
                'posting_type'       => 'STANDARD',
                'reference_id'       => $ref,
                'source_pipeline'    => 'MANUAL_ENTRY',
                'transaction_status' => 'SUCCESSFUL',
                'memo'               => $g['meta']['memo'],
            ], $g['lines']);
            $n++;
        }
        return $n;
    }
}
