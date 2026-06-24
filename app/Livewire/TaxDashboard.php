<?php

namespace App\Livewire;

use App\Models\Company;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TaxDashboard extends Component
{
    public string $language       = 'FR';
    public int    $selectedMonth;
    public int    $selectedYear;
    public array  $taxMetrics     = [];

    public function mount(): void
    {
        $this->selectedMonth = (int) now()->format('m');
        $this->selectedYear  = (int) now()->format('Y');
        $this->language      = session('opes_lang', 'FR');
        $this->calculateCameroonTaxMetrics();
    }

    public function toggleLanguage(): void
    {
        $this->language = $this->language === 'FR' ? 'EN' : 'FR';
        session(['opes_lang' => $this->language]);
    }

    public function updatedSelectedMonth(): void
    {
        $this->calculateCameroonTaxMetrics();
    }

    public function updatedSelectedYear(): void
    {
        $this->calculateCameroonTaxMetrics();
    }

    public function calculateCameroonTaxMetrics(): void
    {
        $company = $this->resolveCompany();

        if (! $company) {
            $this->taxMetrics = $this->emptyMetrics();
            return;
        }

        $periodStart = sprintf('%04d-%02d-01', $this->selectedYear, $this->selectedMonth);
        $periodEnd   = date('Y-m-t', strtotime($periodStart));

        // Output TVA & CAC collected (Class 443/448 credit balances)
        $outputVat = $this->sumAccountCredit(['443100'], $company->id, $periodStart, $periodEnd);
        $outputCac = $this->sumAccountCredit(['448600'], $company->id, $periodStart, $periodEnd);

        // Gross turnover HT from Class 7 (revenue) account credits
        $turnoverHt = $this->sumAccountCredit(['701100', '706000'], $company->id, $periodStart, $periodEnd);

        // Cross-check: derive from reverse-engineering if accounts not separated
        if ($this->isZero($turnoverHt) && ! $this->isZero($outputVat)) {
            // TVA = HT × 0.175, so HT = TVA / 0.175
            $turnoverHt = (string) BigDecimal::of($outputVat)
                ->dividedBy(BigDecimal::of('0.175'), 2, RoundingMode::HalfUp);
        }

        // Input VAT on purchases (Class 445 debit = deductible input VAT)
        $inputVatGross = $this->sumAccountDebit(['445100', '445200'], $company->id, $periodStart, $periodEnd);

        // Apply prorata
        $prorataMultiplier = $company->prorataMultiplier();
        $inputVatRecoverable = (string) BigDecimal::of($inputVatGross)
            ->multipliedBy($prorataMultiplier)
            ->toScale(2, RoundingMode::HalfUp);

        // Net VAT to remit
        $totalOutputTax = (string) BigDecimal::of($outputVat)
            ->plus(BigDecimal::of($outputCac))
            ->toScale(2, RoundingMode::HalfUp);

        $netVatToRemit = (string) BigDecimal::of($totalOutputTax)
            ->minus(BigDecimal::of($inputVatRecoverable))
            ->toScale(2, RoundingMode::HalfUp);

        if (BigDecimal::of($netVatToRemit)->isNegative()) {
            $netVatToRemit = '0.00';
        }

        // Deductible expenses HT (Class 6 debits)
        $expensesHt = $this->sumClass6Debit($company->id, $periodStart, $periodEnd);

        // Minimum tax acompte (IS acompte provisionnel)
        $installmentRate = $company->tax_regime === 'REEL'
            ? BigDecimal::of('0.022')
            : BigDecimal::of('0.055');

        $minimumTaxInstallment = (string) BigDecimal::of($turnoverHt)
            ->multipliedBy($installmentRate)
            ->toScale(2, RoundingMode::HalfUp);

        $totalFiscalProvision = (string) BigDecimal::of($netVatToRemit)
            ->plus(BigDecimal::of($minimumTaxInstallment))
            ->toScale(2, RoundingMode::HalfUp);

        $this->taxMetrics = [
            'company_name'             => $company->name,
            'tax_center'               => $company->tax_center,
            'tax_regime'               => $company->tax_regime,
            'prorata_coefficient'      => $company->vat_prorata_coefficient,
            'base_turnover_ht'         => $turnoverHt,
            'output_vat_collected'     => $outputVat,
            'output_cac_collected'     => $outputCac,
            'total_output_tax'         => $totalOutputTax,
            'deductible_expenses_ht'   => $expensesHt,
            'input_vat_gross'          => $inputVatGross,
            'input_vat_recoverable'    => $inputVatRecoverable,
            'net_vat_to_remit'         => $netVatToRemit,
            'installment_rate'         => $installmentRate->toScale(4, RoundingMode::HalfUp)->__toString(),
            'minimum_tax_installment'  => $minimumTaxInstallment,
            'total_fiscal_provision'   => $totalFiscalProvision,
            'filing_deadline'          => $this->filingDeadline(),
        ];
    }

    public function render()
    {
        return view('livewire.tax-dashboard')->layout('layouts.app');
    }

    // -------------------------------------------------------------------------

    private function resolveCompany(): ?Company
    {
        $user = Auth::user();
        if ($user && $user->company_id) {
            return Company::find($user->company_id);
        }
        return Company::first();
    }

    private function sumAccountCredit(array $codes, int $companyId, string $from, string $to): string
    {
        $result = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('syscohada_accounts', 'syscohada_accounts.id', '=', 'journal_lines.syscohada_account_id')
            ->where('journal_entries.company_id', $companyId)
            ->whereBetween('journal_entries.posting_date', [$from, $to])
            ->where('journal_entries.transaction_status', 'SUCCESSFUL')
            ->whereIn('syscohada_accounts.code', $codes)
            ->sum('journal_lines.credit');

        return number_format((float) $result, 2, '.', '');
    }

    private function sumAccountDebit(array $codes, int $companyId, string $from, string $to): string
    {
        $result = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('syscohada_accounts', 'syscohada_accounts.id', '=', 'journal_lines.syscohada_account_id')
            ->where('journal_entries.company_id', $companyId)
            ->whereBetween('journal_entries.posting_date', [$from, $to])
            ->where('journal_entries.transaction_status', 'SUCCESSFUL')
            ->whereIn('syscohada_accounts.code', $codes)
            ->sum('journal_lines.debit');

        return number_format((float) $result, 2, '.', '');
    }

    private function sumClass6Debit(int $companyId, string $from, string $to): string
    {
        $result = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('syscohada_accounts', 'syscohada_accounts.id', '=', 'journal_lines.syscohada_account_id')
            ->where('journal_entries.company_id', $companyId)
            ->whereBetween('journal_entries.posting_date', [$from, $to])
            ->where('journal_entries.transaction_status', 'SUCCESSFUL')
            ->where('syscohada_accounts.class_digit', 6)
            ->sum('journal_lines.debit');

        return number_format((float) $result, 2, '.', '');
    }

    private function isZero(string $value): bool
    {
        return BigDecimal::of($value)->isZero();
    }

    private function filingDeadline(): string
    {
        $next = $this->selectedMonth === 12
            ? sprintf('%04d-01-15', $this->selectedYear + 1)
            : sprintf('%04d-%02d-15', $this->selectedYear, $this->selectedMonth + 1);

        return $next;
    }

    private function emptyMetrics(): array
    {
        return [
            'company_name'             => '',
            'tax_center'               => '',
            'tax_regime'               => 'REEL',
            'prorata_coefficient'      => '100.00',
            'base_turnover_ht'         => '0.00',
            'output_vat_collected'     => '0.00',
            'output_cac_collected'     => '0.00',
            'total_output_tax'         => '0.00',
            'deductible_expenses_ht'   => '0.00',
            'input_vat_gross'          => '0.00',
            'input_vat_recoverable'    => '0.00',
            'net_vat_to_remit'         => '0.00',
            'installment_rate'         => '0.0220',
            'minimum_tax_installment'  => '0.00',
            'total_fiscal_provision'   => '0.00',
            'filing_deadline'          => $this->filingDeadline(),
        ];
    }
}
