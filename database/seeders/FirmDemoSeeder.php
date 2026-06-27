<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Firm;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\SyscohadaAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FirmDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo firm ────────────────────────────────────────────────────────
        $firm = Firm::firstOrCreate(
            ['slug' => 'cabinet-nkeng-demo'],
            [
                'name'         => 'Cabinet Nkeng & Associés',
                'oecam_number' => 'OEC-2021-0042',
                'email'        => 'contact@cabinet-nkeng.cm',
                'phone'        => '+237 6 70 11 22 33',
                'address'      => 'Rue Drouot, Bonanjo, Douala',
                'city'         => 'Douala',
                'max_clients'  => 50,
                'is_active'    => true,
            ]
        );

        // ── Firm partner (demo login) ─────────────────────────────────────
        $partner = User::firstOrCreate(
            ['email' => 'cabinet@demo.cm'],
            [
                'company_id' => null,
                'name'       => 'Alain Nkeng',
                'password'   => Hash::make('demo1234'),
                'role'       => 'FIRM_ACCOUNTANT',
            ]
        );

        if (! $firm->staff()->where('users.id', $partner->id)->exists()) {
            $firm->staff()->attach($partner->id, ['firm_role' => 'PARTNER', 'is_active' => true]);
        }

        // ── Junior accountant ─────────────────────────────────────────────
        $junior = User::firstOrCreate(
            ['email' => 'junior@demo.cm'],
            [
                'company_id' => null,
                'name'       => 'Sandrine Ebongué',
                'password'   => Hash::make('demo1234'),
                'role'       => 'FIRM_ACCOUNTANT',
            ]
        );

        if (! $firm->staff()->where('users.id', $junior->id)->exists()) {
            $firm->staff()->attach($junior->id, ['firm_role' => 'JUNIOR', 'is_active' => true]);
        }

        // ── Client companies ──────────────────────────────────────────────
        $clients = [
            [
                'name'            => 'Brasseries du Wouri Sarl',
                'niu'             => 'M081000021',
                'rccm'            => 'RC/DLA/2015/B/2210',
                'tax_regime'      => 'REEL',
                'tax_center'      => 'DGE Douala',
                'email'           => 'compta@brasseries-wouri.cm',
                'address'         => 'Zone Industrielle de Bassa, Douala',
                'engagement_type' => 'FULL_OUTSOURCE',
                'billing_mode'    => 'FIRM_PAYS',
                'entries'         => [
                    ['account' => '701100', 'label' => 'Ventes de marchandises', 'debit' => 0,         'credit' => 8_500_000],
                    ['account' => '443100', 'label' => 'TVA collectée 19,25%',  'debit' => 0,         'credit' => 1_636_250],
                    ['account' => '411100', 'label' => 'Clients',               'debit' => 10_136_250,'credit' => 0],
                    ['account' => '601100', 'label' => 'Achats de marchandises','debit' => 4_200_000, 'credit' => 0],
                    ['account' => '641100', 'label' => 'Salaires bruts',        'debit' => 1_800_000, 'credit' => 0],
                ],
            ],
            [
                'name'            => 'AgriTech Cameroun SA',
                'niu'             => 'M082000055',
                'rccm'            => 'RC/DLA/2018/B/0887',
                'tax_regime'      => 'REEL',
                'tax_center'      => 'CIME Douala',
                'email'           => 'finance@agritech-cm.com',
                'address'         => 'Cité des Palmiers, Douala',
                'engagement_type' => 'TAX_ONLY',
                'billing_mode'    => 'CLIENT_PAYS',
                'entries'         => [
                    ['account' => '701100', 'label' => 'Ventes de produits agricoles', 'debit' => 0,        'credit' => 5_200_000],
                    ['account' => '443100', 'label' => 'TVA collectée 19,25%',         'debit' => 0,        'credit' => 1_001_000],
                    ['account' => '411100', 'label' => 'Clients',                      'debit' => 6_201_000,'credit' => 0],
                    ['account' => '601100', 'label' => 'Achats intrants agricoles',    'debit' => 2_100_000,'credit' => 0],
                ],
            ],
            [
                'name'            => 'Transport Ngando & Fils',
                'niu'             => 'P083000109',
                'rccm'            => 'RC/YAO/2020/B/0344',
                'tax_regime'      => 'LIBERATOIRE',
                'tax_center'      => 'CIME Yaoundé',
                'email'           => 'ngando.transport@yahoo.fr',
                'address'         => 'Quartier Mvog-Mbi, Yaoundé',
                'engagement_type' => 'REVIEW_ONLY',
                'billing_mode'    => 'FIRM_PAYS',
                'entries'         => [
                    ['account' => '706100', 'label' => 'Prestations de transport', 'debit' => 0,       'credit' => 1_850_000],
                    ['account' => '411100', 'label' => 'Clients divers',           'debit' => 1_850_000,'credit' => 0],
                    ['account' => '615100', 'label' => 'Entretien véhicules',      'debit' => 420_000, 'credit' => 0],
                ],
            ],
            [
                'name'            => 'Cabinet Médical Dr. Eto',
                'niu'             => 'P084000302',
                'rccm'            => 'RC/DLA/2022/B/1190',
                'tax_regime'      => 'REEL',
                'tax_center'      => 'CIME Douala',
                'email'           => 'compta@clinique-eto.cm',
                'address'         => 'Rue Bonanjo 48, Douala',
                'engagement_type' => 'PAYROLL_ONLY',
                'billing_mode'    => 'HYBRID',
                'entries'         => [
                    ['account' => '706100', 'label' => 'Honoraires médicaux',   'debit' => 0,       'credit' => 3_400_000],
                    ['account' => '411100', 'label' => 'Patients / créances',   'debit' => 3_400_000,'credit' => 0],
                    ['account' => '641100', 'label' => 'Salaires personnel',    'debit' => 1_200_000,'credit' => 0],
                    ['account' => '641300', 'label' => 'Charges sociales CNPS', 'debit' => 144_000, 'credit' => 0],
                ],
            ],
        ];

        foreach ($clients as $i => $cfg) {
            $company = Company::firstOrCreate(
                ['niu' => $cfg['niu']],
                [
                    'name'                => $cfg['name'],
                    'rccm'                => $cfg['rccm'],
                    'tax_regime'          => $cfg['tax_regime'],
                    'tax_center'          => $cfg['tax_center'],
                    'email'               => $cfg['email'],
                    'address'             => $cfg['address'],
                    'country_code'        => 'CM',
                    'subscription_status' => 'ACTIVE',
                    'plan_slug'           => 'PRO',
                    'vat_prorata_coefficient' => 100.00,
                ]
            );

            // Attach to firm portfolio
            if (! $firm->clients()->where('companies.id', $company->id)->exists()) {
                $firm->clients()->attach($company->id, [
                    'engagement_type'        => $cfg['engagement_type'],
                    'billing_mode'           => $cfg['billing_mode'],
                    'assigned_accountant_id' => $junior->id,
                    'is_active'              => true,
                    'onboarded_at'           => now()->subMonths(6 - $i),
                ]);
            }

            // Give firm staff access to this company
            foreach ([$partner, $junior] as $staff) {
                if (! $staff->companies()->where('companies.id', $company->id)->exists()) {
                    $staff->companies()->attach($company->id, ['role' => 'ACCOUNTANT', 'is_default' => false]);
                }
            }

            // Seed some journal entries with realistic recent dates
            if (JournalEntry::where('company_id', $company->id)->exists()) {
                continue; // idempotent — don't double-seed
            }

            // Pre-resolve account IDs for this client's entry lines
            $accountMap = collect($cfg['entries'])
                ->pluck('account')
                ->unique()
                ->mapWithKeys(fn($code) => [
                    $code => SyscohadaAccount::where('code', $code)->value('id'),
                ]);

            foreach (range(0, 2) as $monthOffset) {
                $postingDate = Carbon::now()->startOfMonth()->subMonths($monthOffset)->day(rand(3, 25));

                $entry = JournalEntry::create([
                    'company_id'      => $company->id,
                    'reference_id'    => 'ECR-' . strtoupper(Str::random(6)),
                    'memo'            => $cfg['entries'][0]['label'] . ' — ' . $postingDate->format('M Y'),
                    'posting_date'    => $postingDate,
                    'source_pipeline'    => 'MANUAL_CASH',
                    'transaction_status' => 'SUCCESSFUL',
                    'dgi_sync_status' => $monthOffset === 0 ? 'PENDING' : 'APPROVED',
                ]);

                foreach ($cfg['entries'] as $line) {
                    $accountId = $accountMap[$line['account']] ?? null;
                    if (! $accountId) continue; // skip if SYSCOHADA account not seeded

                    JournalLine::create([
                        'journal_entry_id'   => $entry->id,
                        'syscohada_account_id'=> $accountId,
                        'description'        => $line['label'],
                        'debit'              => $line['debit'],
                        'credit'             => $line['credit'],
                    ]);
                }
            }
        }
    }
}
