<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\SyscohadaAccount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Populates the 4 demo firm client companies with 12 months of
 * realistic SYSCOHADA journal data so the firm portal reports
 * and the individual /app ledgers show meaningful numbers.
 *
 * Run after FirmDemoSeeder.
 */
class FirmClientDataSeeder extends Seeder
{
    private array $acctIds = [];

    public function run(): void
    {
        // Pre-load all account IDs by code for fast lookup
        SyscohadaAccount::all()->each(function ($a) {
            $this->acctIds[$a->code] = $a->id;
        });

        // Clear any existing sparse entries from FirmDemoSeeder
        $clientNius = ['M081000021', 'M082000055', 'P083000109', 'P084000302'];
        $companies  = Company::whereIn('niu', $clientNius)->get()->keyBy('niu');

        JournalEntry::whereIn('company_id', $companies->pluck('id'))->forceDelete();

        // ── Company 1: Brasseries du Wouri Sarl ──────────────────────────
        // Distribution / wholesale. Heavy TVA. DGE client. 12 months data.
        $this->seedBrasseriesWouri($companies['M081000021']);

        // ── Company 2: AgriTech Cameroun SA ──────────────────────────────
        // Agricultural produce sales + processing. Mixed TVA. 12 months.
        $this->seedAgritech($companies['M082000055']);

        // ── Company 3: Transport Ngando & Fils ───────────────────────────
        // Libératoire regime transport. No TVA. Cash heavy. 10 months.
        $this->seedTransportNgando($companies['P083000109']);

        // ── Company 4: Cabinet Médical Dr. Eto ───────────────────────────
        // Medical services. Payroll heavy. CNPS contributions. 8 months.
        $this->seedCabinetMedical($companies['P084000302']);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function seedBrasseriesWouri(Company $company): void
    {
        // Monthly revenue ramp: starts ~12M XAF/month, grows ~8% QoQ
        $monthlyRevenue = [
            10_800_000, 11_200_000, 11_900_000, // Q1
            12_500_000, 13_100_000, 13_800_000, // Q2
            14_200_000, 14_900_000, 15_600_000, // Q3
            16_100_000, 16_800_000, 17_500_000, // Q4
        ];

        foreach (range(11, 0) as $mAgo) {
            $month   = Carbon::now()->startOfMonth()->subMonths($mAgo);
            $revenue = $monthlyRevenue[11 - $mAgo];
            $tva     = round($revenue * 0.1925, 0);       // 19.25% TVA
            $cogs    = round($revenue * 0.48, 0);          // 48% cost of goods
            $freight = round($revenue * 0.04, 0);
            $salaries= 2_400_000;
            $cnps    = round($salaries * 0.1688, 0);       // 16.88% employer
            $utilities = 380_000;
            $marketing = 150_000;

            // Sales invoice
            $this->entry($company, $month->copy()->day(5), 'MANUAL_INVOICE',
                'Factures ventes — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                $mAgo > 0 ? 'APPROVED' : 'PENDING', [
                    ['411100', $revenue + $tva, 0],     // Clients (TTC)
                    ['701100', 0, $revenue],             // CA ventes
                    ['443100', 0, $tva],                 // TVA collectée
                ]);

            // Stock purchase
            $this->entry($company, $month->copy()->day(8), 'MANUAL_BANK',
                'Achats marchandises — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['601100', $cogs, 0],
                    ['445200', round($cogs * 0.1925, 0), 0],  // TVA récupérable
                    ['401100', 0, $cogs + round($cogs * 0.1925, 0)],
                ]);

            // Payroll (monthly)
            $this->entry($company, $month->copy()->day(28), 'MANUAL_CASH',
                'Paie personnel — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['661100', $salaries, 0],   // Salaires bruts
                    ['664000', $cnps, 0],        // CNPS patronale
                    ['421100', 0, $salaries],    // Personnel — net à payer
                    ['431000', 0, $cnps],        // CNPS à reverser
                ]);

            // Freight & utilities
            $this->entry($company, $month->copy()->day(15), 'MANUAL_CASH',
                'Frais transport & utilities — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['611000', $freight, 0],
                    ['605100', $utilities, 0],
                    ['624100', $marketing, 0],
                    ['571100', 0, $freight + $utilities + $marketing],
                ]);

            // Quarterly CNPS payment (Jan, Apr, Jul, Oct)
            if (in_array($month->month, [1, 4, 7, 10])) {
                $qCnps = $cnps * 3;
                $this->entry($company, $month->copy()->day(14), 'MANUAL_BANK',
                    'Versement CNPS T' . ceil($month->month / 3),
                    'APPROVED', [
                        ['431000', $qCnps, 0],
                        ['521100', 0, $qCnps],
                    ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function seedAgritech(Company $company): void
    {
        // Seasonal agricultural: peaks in harvest months (Oct-Jan), quiet Apr-Jun
        $monthlyRevenue = [
            5_800_000,  6_200_000,  4_100_000, // Jan-Mar (post-harvest winding down)
            2_900_000,  3_100_000,  2_700_000, // Apr-Jun (lean season)
            3_500_000,  4_200_000,  5_600_000, // Jul-Sep (new season ramp)
            7_800_000,  8_400_000,  9_200_000, // Oct-Dec (peak harvest)
        ];

        foreach (range(11, 0) as $mAgo) {
            $month   = Carbon::now()->startOfMonth()->subMonths($mAgo);
            $revenue = $monthlyRevenue[11 - $mAgo];
            $tva     = round($revenue * 0.1925, 0);
            $rawMat  = round($revenue * 0.42, 0);
            $salaries= 1_600_000;
            $cnps    = round($salaries * 0.1688, 0);
            $transport = round($revenue * 0.06, 0);

            // Sales
            $this->entry($company, $month->copy()->day(10), 'MANUAL_INVOICE',
                'Ventes produits agricoles — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                $mAgo > 0 ? 'APPROVED' : 'PENDING', [
                    ['411100', $revenue + $tva, 0],
                    ['701100', 0, $revenue],
                    ['443100', 0, $tva],
                ]);

            // Raw material purchases (heavier in harvest months)
            $this->entry($company, $month->copy()->day(3), 'MANUAL_CASH',
                'Achats matières premières — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['602100', $rawMat, 0],
                    ['401100', 0, $rawMat],
                ]);

            // Payroll
            $this->entry($company, $month->copy()->day(27), 'MANUAL_BANK',
                'Paie personnel — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['661100', $salaries, 0],
                    ['664000', $cnps, 0],
                    ['421100', 0, $salaries],
                    ['431000', 0, $cnps],
                ]);

            // Transport costs
            $this->entry($company, $month->copy()->day(20), 'MANUAL_CASH',
                'Frais transport marchandises — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['611000', $transport, 0],
                    ['571100', 0, $transport],
                ]);

            // Semi-annual IS acompte (Feb, Aug)
            if (in_array($month->month, [2, 8])) {
                $isAcompte = round($revenue * 0.055, 0); // ~5.5% estimated
                $this->entry($company, $month->copy()->day(15), 'MANUAL_BANK',
                    'Acompte IS — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                    'APPROVED', [
                        ['442100', $isAcompte, 0],
                        ['521100', 0, $isAcompte],
                    ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function seedTransportNgando(Company $company): void
    {
        // Libératoire regime — no TVA, mostly cash, simpler bookkeeping
        // Only 10 months of data (onboarded later)
        $monthlyRevenue = [
            1_850_000, 1_920_000, 2_050_000, 1_780_000, 2_100_000,
            2_200_000, 1_950_000, 2_300_000, 2_450_000, 2_600_000,
        ];

        foreach (range(9, 0) as $mAgo) {
            $month   = Carbon::now()->startOfMonth()->subMonths($mAgo);
            $revenue = $monthlyRevenue[9 - $mAgo];
            $fuel    = round($revenue * 0.28, 0);
            $maint   = round($revenue * 0.10, 0);
            $salary  = 680_000;  // 2 drivers

            // Transport revenue (no TVA — libératoire)
            $this->entry($company, $month->copy()->day(4), 'MANUAL_CASH',
                'Recettes transport — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                $mAgo > 0 ? 'APPROVED' : 'PENDING', [
                    ['571100', $revenue, 0],
                    ['706000', 0, $revenue],
                ]);

            // Fuel
            $this->entry($company, $month->copy()->day(12), 'MANUAL_CASH',
                'Carburant & lubrifiants — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['618100', $fuel, 0],
                    ['571100', 0, $fuel],
                ]);

            // Vehicle maintenance
            $this->entry($company, $month->copy()->day(22), 'MANUAL_CASH',
                'Entretien véhicules — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['618100', $maint, 0],
                    ['571100', 0, $maint],
                ]);

            // Driver salaries (bi-monthly cash payment)
            $this->entry($company, $month->copy()->day(29), 'MANUAL_CASH',
                'Salaires chauffeurs — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['661100', $salary, 0],
                    ['571100', 0, $salary],
                ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function seedCabinetMedical(Company $company): void
    {
        // Medical services — steady revenue, high payroll, CNPS quarterly
        // 8 months of data (newest client)
        $monthlyRevenue = [
            3_200_000, 3_400_000, 3_600_000, 3_500_000,
            3_750_000, 3_900_000, 4_100_000, 4_300_000,
        ];

        foreach (range(7, 0) as $mAgo) {
            $month   = Carbon::now()->startOfMonth()->subMonths($mAgo);
            $revenue = $monthlyRevenue[7 - $mAgo];
            $tva     = round($revenue * 0.1925, 0);
            $medStaff= 2_800_000;  // 4 medical staff
            $cnps    = round($medStaff * 0.1688, 0);
            $supplies= 420_000;

            // Consultation fees
            $this->entry($company, $month->copy()->day(6), 'MANUAL_INVOICE',
                'Honoraires médicaux & consultations — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                $mAgo > 0 ? 'APPROVED' : 'PENDING', [
                    ['411100', $revenue + $tva, 0],
                    ['706000', 0, $revenue],
                    ['443100', 0, $tva],
                ]);

            // Payroll (medical professionals)
            $this->entry($company, $month->copy()->day(25), 'MANUAL_BANK',
                'Rémunération personnel médical — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['661100', $medStaff, 0],
                    ['664000', $cnps, 0],
                    ['421100', 0, $medStaff],
                    ['431000', 0, $cnps],
                ]);

            // Medical supplies
            $this->entry($company, $month->copy()->day(10), 'MANUAL_CASH',
                'Fournitures médicales — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['601100', $supplies, 0],
                    ['571100', 0, $supplies],
                ]);

            // IRPP/CAC on salaries (monthly)
            $irpp = round($medStaff * 0.075, 0); // simplified ~7.5% average
            $this->entry($company, $month->copy()->day(14), 'MANUAL_BANK',
                'IRPP & CAC sur salaires — ' . $month->locale('fr')->isoFormat('MMMM YYYY'),
                'APPROVED', [
                    ['447000', $irpp, 0],
                    ['521100', 0, $irpp],
                ]);

            // Quarterly CNPS (Jan, Apr, Jul, Oct)
            if (in_array($month->month, [1, 4, 7, 10]) && $mAgo >= 1) {
                $qCnps = $cnps * 3;
                $this->entry($company, $month->copy()->day(13), 'MANUAL_BANK',
                    'Versement CNPS trimestriel — T' . ceil($month->month / 3),
                    'APPROVED', [
                        ['431000', $qCnps, 0],
                        ['521100', 0, $qCnps],
                    ]);
            }

            // Depreciation (quarterly for medical equipment)
            if (in_array($month->month, [3, 6, 9, 12])) {
                $depreciation = 185_000;
                $this->entry($company, $month->copy()->day(31)->endOfMonth(), 'MANUAL_CASH',
                    'Dotation amortissement matériel médical',
                    'APPROVED', [
                        ['681200', $depreciation, 0],
                        ['285110', 0, $depreciation],
                    ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function entry(
        Company  $company,
        Carbon   $date,
        string   $pipeline,
        string   $memo,
        string   $dgiStatus,
        array    $lines
    ): void {
        $entry = JournalEntry::create([
            'company_id'         => $company->id,
            'reference_id'       => strtoupper(Str::random(3)) . '-' . $date->format('Ymd') . '-' . strtoupper(Str::random(4)),
            'memo'               => $memo,
            'posting_date'       => $date->toDateString(),
            'source_pipeline'    => $pipeline,
            'transaction_status' => 'SUCCESSFUL',
            'dgi_sync_status'    => $dgiStatus,
        ]);

        foreach ($lines as [$code, $debit, $credit]) {
            $acctId = $this->acctIds[$code] ?? null;
            if (! $acctId) {
                $this->command->warn("Account code $code not found — skipping line.");
                continue;
            }
            JournalLine::create([
                'journal_entry_id'    => $entry->id,
                'syscohada_account_id'=> $acctId,
                'debit'               => $debit,
                'credit'              => $credit,
                'description'         => $memo,
            ]);
        }
    }
}
