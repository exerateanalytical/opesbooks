<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\FixedAsset;
use App\Models\Budget;
use App\Models\User;
use Database\Seeders\SyscohadaAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedModulesTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $owner;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SyscohadaAccountSeeder::class);

        $this->company = Company::factory()->create([
            'niu'        => 'P012345678901A',
            'tax_center' => 'CIME Douala I',
            'tax_regime' => 'REEL',
        ]);

        $this->owner = User::factory()->create([
            'company_id' => $this->company->id,
            'role'       => 'OWNER',
        ]);

        $this->token = $this->owner->createToken('test')->plainTextToken;
    }

    private function auth(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    // ── Supplier Invoices ────────────────────────────────────────────────

    public function test_supplier_invoice_store_posts_journal_entry(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->company->id]);

        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/supplier-invoices",
            [
                'supplier_id'    => $supplier->id,
                'invoice_number' => 'FINV-001',
                'invoice_date'   => '2026-06-01',
                'due_date'       => '2026-06-30',
                'amount_ht'      => 100000,
                'tva_amount'     => 17500,
                'expense_account'=> '601100',
            ]
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('supplier_invoices', [
            'invoice_number' => 'FINV-001',
            'amount_ht'      => 100000,
        ]);
        // Journal entry must have been posted
        $this->assertNotNull($response->json('journal_entry_id'));
    }

    public function test_supplier_invoice_pay_posts_payment_entry(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->company->id]);

        $invoice = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/supplier-invoices",
            [
                'supplier_id'    => $supplier->id,
                'invoice_number' => 'FINV-002',
                'invoice_date'   => '2026-06-01',
                'due_date'       => '2026-06-30',
                'amount_ht'      => 50000,
                'tva_amount'     => 8750,
                'expense_account'=> '621100',
            ]
        )->json();

        $pay = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/supplier-invoices/{$invoice['id']}/pay",
            ['payment_account' => '521100', 'payment_ref' => 'VIR-001']
        );

        $pay->assertOk();
        $this->assertEquals('PAID', $pay->json('status'));
        $this->assertDatabaseHas('journal_entries', ['reference_id' => 'PAY-FINV-002']);
    }

    public function test_supplier_invoice_draft_can_be_deleted(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->company->id]);

        // Create a draft directly (bypass post() to keep it DRAFT)
        $invoice = SupplierInvoice::create([
            'company_id'     => $this->company->id,
            'supplier_id'    => $supplier->id,
            'invoice_number' => 'FINV-DRAFT',
            'invoice_date'   => '2026-06-01',
            'due_date'       => '2026-06-30',
            'amount_ht'      => 10000,
            'tva_amount'     => 1750,
            'amount_ttc'     => 11750,
            'net_payable'    => 11750,
            'status'         => 'DRAFT',
        ]);

        $this->withHeaders($this->auth())
            ->deleteJson("/api/v1/companies/{$this->company->id}/supplier-invoices/{$invoice->id}")
            ->assertOk();

        $this->assertSoftDeleted('supplier_invoices', ['id' => $invoice->id]);
    }

    // ── Fixed Assets ─────────────────────────────────────────────────────

    public function test_fixed_asset_store_posts_acquisition_entry(): void
    {
        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/fixed-assets",
            [
                'name'                   => 'MacBook Pro',
                'category'               => 'IT_EQUIPMENT',
                'syscohada_account_code' => '245100',
                'acquisition_date'       => '2026-01-15',
                'acquisition_cost'       => 1500000,
                'useful_life_months'     => 36,
                'residual_value'         => 100000,
                'credit_account'         => '521100',
            ]
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('fixed_assets', ['name' => 'MacBook Pro', 'acquisition_cost' => 1500000]);
        $this->assertNotNull($response->json('acquisition_journal_entry_id'));

        // Book value = cost - accumulated_depreciation - residual = 1500000 - 0 - 100000
        $this->assertEquals(1400000, $response->json('book_value'));
    }

    public function test_monthly_depreciation_amount_is_correct(): void
    {
        $asset = FixedAsset::create([
            'company_id'             => $this->company->id,
            'name'                   => 'Véhicule de service',
            'category'               => 'VEHICLE',
            'syscohada_account_code' => '244100',
            'acquisition_date'       => '2026-01-01',
            'acquisition_cost'       => 6000000,
            'residual_value'         => 0,
            'useful_life_months'     => 60,
            'depreciation_method'    => 'LINEAR',
            'accumulated_depreciation' => 0,
            'is_active'              => true,
        ]);

        // Monthly = (6,000,000 - 0) / 60 = 100,000 XAF
        $this->assertEquals(100000.0, $asset->monthlyDepreciation());
        $this->assertEquals(6000000.0, $asset->bookValue());
        $this->assertFalse($asset->isFullyDepreciated());
    }

    public function test_run_depreciation_creates_entry_and_increments_accumulated(): void
    {
        FixedAsset::create([
            'company_id'             => $this->company->id,
            'name'                   => 'Matériel industriel',
            'category'               => 'MACHINERY',
            'syscohada_account_code' => '241100',
            'acquisition_date'       => '2025-01-01',
            'acquisition_cost'       => 1200000,
            'residual_value'         => 0,
            'useful_life_months'     => 12,
            'depreciation_method'    => 'LINEAR',
            'accumulated_depreciation' => 0,
            'is_active'              => true,
        ]);

        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/fixed-assets/run-depreciation",
            ['month' => 1, 'year' => 2026]
        );

        $response->assertOk();
        $this->assertEquals(1, $response->json('processed'));
        $this->assertDatabaseHas('depreciation_entries', ['period_month' => 1, 'period_year' => 2026]);
    }

    // ── Budget ───────────────────────────────────────────────────────────

    public function test_budget_can_be_created_and_retrieved(): void
    {
        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/budgets",
            [
                'name'        => 'Budget 2026',
                'fiscal_year' => 2026,
                'lines'       => [
                    ['account_code' => '601100', 'period_month' => 1, 'budgeted_amount' => 500000],
                    ['account_code' => '601100', 'period_month' => 2, 'budgeted_amount' => 500000],
                ],
            ]
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('budgets', ['fiscal_year' => 2026, 'name' => 'Budget 2026']);
        $this->assertDatabaseHas('budget_lines', ['account_code' => '601100', 'period_month' => 1, 'budgeted_amount' => 500000]);
    }

    public function test_budget_variance_returns_correct_structure(): void
    {
        $budget = Budget::create([
            'company_id'  => $this->company->id,
            'fiscal_year' => 2026,
            'name'        => 'Test Budget',
            'status'      => 'ACTIVE',
        ]);
        $budget->lines()->create(['account_code' => '701100', 'period_month' => 1, 'budgeted_amount' => 1000000]);

        $response = $this->withHeaders($this->auth())->getJson(
            "/api/v1/companies/{$this->company->id}/budgets/{$budget->id}/variance"
        );

        $response->assertOk();
        $this->assertArrayHasKey('fiscal_year', $response->json());
        $this->assertArrayHasKey('lines', $response->json());
        $this->assertNotEmpty($response->json('lines'));
        $line = $response->json('lines.0');
        $this->assertArrayHasKey('total_budgeted', $line);
        $this->assertArrayHasKey('total_actual', $line);
        $this->assertArrayHasKey('total_variance', $line);
    }

    // ── DSF Export ───────────────────────────────────────────────────────

    public function test_dsf_generate_returns_all_five_tables(): void
    {
        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/exports/dsf",
            ['fiscal_year' => 2025]
        );

        $response->assertOk();
        $this->assertArrayHasKey('meta', $response->json());
        $this->assertArrayHasKey('table_1_compte_resultat', $response->json());
        $this->assertArrayHasKey('table_2_bilan_actif', $response->json());
        $this->assertArrayHasKey('table_3_bilan_passif', $response->json());
        $this->assertArrayHasKey('table_4_balance_generale', $response->json());
        $this->assertArrayHasKey('table_5_effectifs', $response->json());
        $this->assertEquals(2025, $response->json('meta.fiscal_year'));
    }

    public function test_tva_monthly_return_calculates_correctly(): void
    {
        // Post some TVA entries manually via journal service
        $poster = app(\App\Services\JournalPostingService::class);
        $poster->post([
            'company_id'   => $this->company->id,
            'posting_date' => '2026-05-15',
            'reference_id' => 'SALE-TVA-TEST',
            'source_pipeline' => 'MANUAL_BANK',
            'memo'         => 'Test TVA',
            'posting_type' => 'STANDARD',
        ], [
            ['account_code' => '411100', 'debit' => 119250, 'credit' => 0],
            ['account_code' => '701100', 'debit' => 0,      'credit' => 100000],
            ['account_code' => '443100', 'debit' => 0,      'credit' => 17500],
            ['account_code' => '448600', 'debit' => 0,      'credit' => 1750],
        ]);

        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/exports/tva-monthly",
            ['month' => 5, 'year' => 2026]
        );

        $response->assertOk();
        $this->assertEquals(17500, $response->json('tva_collectee'));
        $this->assertEquals(0,     $response->json('tva_deductible'));
        $this->assertEquals(17500, $response->json('tva_nette_due'));
        $this->assertEquals(1750,  $response->json('cac_net_du'));   // 10% of 17500
        $this->assertEquals(19250, $response->json('total_a_payer'));
    }

    // ── Fiscal Year Close ─────────────────────────────────────────────────

    public function test_fiscal_year_close_carries_profit_to_retained_earnings(): void
    {
        // Post revenue > expenses so net result is a profit
        $poster = app(\App\Services\JournalPostingService::class);
        $poster->post([
            'company_id'   => $this->company->id,
            'posting_date' => '2025-06-15',
            'reference_id' => 'SALE-001',
            'source_pipeline' => 'MANUAL_BANK',
            'memo'         => 'Vente test',
            'posting_type' => 'STANDARD',
        ], [
            ['account_code' => '521100', 'debit' => 200000, 'credit' => 0],
            ['account_code' => '701100', 'debit' => 0,      'credit' => 200000],
        ]);
        $poster->post([
            'company_id'   => $this->company->id,
            'posting_date' => '2025-09-01',
            'reference_id' => 'EXP-001',
            'source_pipeline' => 'MANUAL_BANK',
            'memo'         => 'Charge test',
            'posting_type' => 'STANDARD',
        ], [
            ['account_code' => '601100', 'debit' => 80000,  'credit' => 0],
            ['account_code' => '521100', 'debit' => 0,      'credit' => 80000],
        ]);

        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/fiscal-year/close",
            ['fiscal_year' => 2025]
        );

        $response->assertOk();
        $this->assertEquals('PROFIT', $response->json('type'));
        $this->assertEquals(120000, $response->json('net_result'));

        // 131000 Dr and 121000 Cr should be posted
        $this->assertDatabaseHas('journal_entries', ['reference_id' => 'CLOTURE-2025']);
    }

    public function test_opening_balances_must_be_balanced(): void
    {
        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/fiscal-year/opening-balances",
            [
                'fiscal_year' => 2026,
                'balances'    => [
                    ['account_code' => '521100', 'debit' => 1000000, 'credit' => 0],
                    ['account_code' => '101000', 'debit' => 0,       'credit' => 500000],
                    // Intentionally unbalanced: 1,000,000 debit ≠ 500,000 credit
                ],
            ]
        );

        $response->assertStatus(422);
        $this->assertArrayHasKey('difference', $response->json());
    }

    public function test_balanced_opening_balances_post_successfully(): void
    {
        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/fiscal-year/opening-balances",
            [
                'fiscal_year' => 2026,
                'balances'    => [
                    ['account_code' => '521100', 'debit' => 1000000, 'credit' => 0],
                    ['account_code' => '101000', 'debit' => 0,       'credit' => 1000000],
                ],
            ]
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('journal_entries', ['reference_id' => 'OB-2026']);
    }

    // ── CSV Exports ──────────────────────────────────────────────────────

    public function test_trial_balance_csv_returns_csv_content_type(): void
    {
        $response = $this->withHeaders($this->auth())->get(
            "/api/v1/companies/{$this->company->id}/exports/trial-balance-csv?from=2026-01-01&to=2026-12-31"
        );

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    // ── Chart of Accounts ────────────────────────────────────────────────

    public function test_chart_of_accounts_returns_seeded_accounts(): void
    {
        $response = $this->withHeaders($this->auth())->getJson(
            "/api/v1/companies/{$this->company->id}/accounts"
        );

        $response->assertOk();
        $this->assertNotEmpty($response->json());
        // 101000 Capital Social should be present
        $codes = array_column($response->json(), 'code');
        $this->assertContains('101000', $codes);
        $this->assertContains('521100', $codes);
    }

    public function test_custom_account_can_be_added(): void
    {
        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/accounts",
            ['code' => '699999', 'label' => 'Compte test personnalisé', 'class_digit' => 6]
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('syscohada_accounts', ['code' => '699999']);
    }

    public function test_duplicate_account_code_is_rejected(): void
    {
        $response = $this->withHeaders($this->auth())->postJson(
            "/api/v1/companies/{$this->company->id}/accounts",
            ['code' => '521100', 'label' => 'Duplicate bank', 'class_digit' => 5]
        );

        $response->assertStatus(422);
    }
}
