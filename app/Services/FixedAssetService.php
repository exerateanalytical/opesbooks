<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DepreciationEntry;
use App\Models\FixedAsset;
use Illuminate\Support\Str;

class FixedAssetService
{
    public function __construct(private JournalPostingService $poster) {}

    /**
     * Post acquisition of a fixed asset.
     * Dr 2xxxxx (asset account)
     * Cr 401100 (supplier payable) or 521100 (bank)
     */
    public function postAcquisition(FixedAsset $asset, string $creditAccountCode): FixedAsset
    {
        $entry = $this->poster->post([
            'company_id'      => $asset->company_id,
            'posting_date'    => $asset->acquisition_date->toDateString(),
            'reference_id'    => 'ACQ-' . strtoupper(Str::random(8)),
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Acquisition immobilisation: {$asset->name}",
            'posting_type'    => 'STANDARD',
        ], [
            ['account_code' => $asset->syscohada_account_code, 'debit' => $asset->acquisition_cost, 'credit' => 0],
            ['account_code' => $creditAccountCode,              'debit' => 0, 'credit' => $asset->acquisition_cost],
        ]);

        $asset->update(['acquisition_journal_entry_id' => $entry->id]);
        return $asset->fresh();
    }

    /**
     * Run monthly depreciation for all active, non-fully-depreciated assets.
     * Called by the scheduler on the 1st of every month.
     * Dr 681xxx (Dotation aux amortissements)
     * Cr 28xxxx (Amortissements cumulés)
     */
    public function runMonthlyDepreciation(int $month, int $year, ?int $companyId = null): int
    {
        $processed = 0;
        $assets = FixedAsset::where('is_active', true)
            ->whereNull('disposal_date')
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->with('company')
            ->get();

        foreach ($assets as $asset) {
            if ($asset->isFullyDepreciated()) continue;

            // Skip if already processed this period
            $exists = DepreciationEntry::where('fixed_asset_id', $asset->id)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->exists();
            if ($exists) continue;

            $amount = min($asset->monthlyDepreciation(), $asset->bookValue());
            if ($amount <= 0) continue;

            try {
                $entry = $this->poster->post([
                    'company_id'      => $asset->company_id,
                    'posting_date'    => sprintf('%d-%02d-01', $year, $month),
                    'reference_id'    => 'DEP-' . sprintf('%d%02d', $year, $month) . '-' . $asset->id,
                    'source_pipeline' => 'MANUAL_BANK',
                    'memo'            => "Amortissement {$month}/{$year} — {$asset->name}",
                    'posting_type'    => 'ADJUSTMENT',
                ], [
                    ['account_code' => '681200', 'debit' => $amount, 'credit' => 0],
                    ['account_code' => $asset->accumulatedDepreciationAccountCode(), 'debit' => 0, 'credit' => $amount],
                ]);

                DepreciationEntry::create([
                    'fixed_asset_id'  => $asset->id,
                    'period_month'    => $month,
                    'period_year'     => $year,
                    'amount'          => $amount,
                    'journal_entry_id'=> $entry->id,
                ]);

                $asset->increment('accumulated_depreciation', $amount);
                $processed++;
            } catch (\Throwable $e) {
                \Log::error("Depreciation failed asset #{$asset->id}: " . $e->getMessage());
            }
        }

        return $processed;
    }

    /**
     * Dispose of a fixed asset.
     * Dr 28xxxx (accumulated depreciation — clear)
     * Dr 654xxx (loss on disposal, if any)
     * Cr 2xxxxx (asset account — remove cost)
     * Cr 521100 (proceeds received)
     * Cr 754xxx (gain on disposal, if any)
     */
    public function dispose(FixedAsset $asset, float $proceeds, string $receiptAccountCode): FixedAsset
    {
        $bookValue  = $asset->bookValue();
        $gainOrLoss = $proceeds - $bookValue;

        $lines = [
            ['account_code' => $asset->accumulatedDepreciationAccountCode(), 'debit' => $asset->accumulated_depreciation, 'credit' => 0],
            ['account_code' => $asset->syscohada_account_code,               'debit' => 0, 'credit' => $asset->acquisition_cost],
        ];

        if ($proceeds > 0) {
            $lines[] = ['account_code' => $receiptAccountCode, 'debit' => $proceeds, 'credit' => 0];
        }

        if ($gainOrLoss > 0) {
            $lines[] = ['account_code' => '754100', 'debit' => 0, 'credit' => $gainOrLoss];
        } elseif ($gainOrLoss < 0) {
            $lines[] = ['account_code' => '654100', 'debit' => abs($gainOrLoss), 'credit' => 0];
        }

        $entry = $this->poster->post([
            'company_id'      => $asset->company_id,
            'posting_date'    => now()->toDateString(),
            'reference_id'    => 'DIS-' . strtoupper(Str::random(8)),
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Cession immobilisation: {$asset->name}",
            'posting_type'    => 'STANDARD',
        ], $lines);

        $asset->update([
            'disposal_date'               => now()->toDateString(),
            'disposal_proceeds'           => $proceeds,
            'disposal_journal_entry_id'   => $entry->id,
            'is_active'                   => false,
        ]);

        return $asset->fresh();
    }
}
