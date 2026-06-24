<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SyscohadaAccount;
use App\Models\User;
use App\Services\JournalPostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class JournalPostingTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SyscohadaAccountSeeder::class);
        $this->company = Company::factory()->create([
            'niu'        => 'P012345678901A',
            'rccm'       => 'RC/DLA/2023/B/1234',
            'tax_center' => 'CIME Douala I',
            'tax_regime' => 'REEL',
        ]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_balanced_entry_posts_successfully(): void
    {
        $poster = app(JournalPostingService::class);

        $entry = $poster->post([
            'company_id'         => $this->company->id,
            'user_id'            => $this->user->id,
            'posting_date'       => '2026-06-24',
            'posting_type'       => 'STANDARD',
            'reference_id'       => 'TEST-001',
            'source_pipeline'    => 'MANUAL_ENTRY',
            'transaction_status' => 'SUCCESSFUL',
            'memo'               => 'Test balanced entry',
        ], [
            ['account_code' => '571100', 'debit' => '50000', 'credit' => '0'],
            ['account_code' => '701100', 'debit' => '0', 'credit' => '50000'],
        ]);

        $this->assertDatabaseHas('journal_entries', ['reference_id' => 'TEST-001']);
        $this->assertTrue($entry->isBalanced());
    }

    public function test_unbalanced_entry_throws_422(): void
    {
        $this->expectException(ValidationException::class);

        $poster = app(JournalPostingService::class);

        $poster->post([
            'company_id'         => $this->company->id,
            'user_id'            => $this->user->id,
            'posting_date'       => '2026-06-24',
            'posting_type'       => 'STANDARD',
            'reference_id'       => 'TEST-002',
            'source_pipeline'    => 'MANUAL_ENTRY',
            'transaction_status' => 'SUCCESSFUL',
            'memo'               => 'Unbalanced test',
        ], [
            ['account_code' => '571100', 'debit' => '50000', 'credit' => '0'],
            ['account_code' => '701100', 'debit' => '0', 'credit' => '40000'],
        ]);
    }

    public function test_momo_callback_queues_payload_and_returns_202(): void
    {
        $response = $this->postJson('/api/v1/ingest/telecom/callback', [
            'company_niu'    => 'P012345678901A',
            'operator'       => 'MTN',
            'transaction_id' => 'MTN-TXN-001',
            'amount'         => 119250,
            'message'        => 'Transfert recu de Client A',
            'date'           => '2026-06-24',
        ]);

        $response->assertStatus(202)
            ->assertJsonFragment(['status' => 'QUEUED']);

        $this->assertDatabaseHas('raw_payload_queue', ['transaction_id' => 'MTN-TXN-001']);
    }

    public function test_duplicate_momo_callback_is_idempotent(): void
    {
        $payload = [
            'company_niu'    => 'P012345678901A',
            'operator'       => 'MTN',
            'transaction_id' => 'MTN-DUP-001',
            'amount'         => 5000,
            'message'        => 'Transfert recu',
            'date'           => '2026-06-24',
        ];

        $this->postJson('/api/v1/ingest/telecom/callback', $payload)->assertStatus(202);
        $response = $this->postJson('/api/v1/ingest/telecom/callback', $payload);

        $response->assertStatus(202)->assertJsonFragment(['status' => 'DUPLICATE']);
        $this->assertEquals(1, \App\Models\RawPayloadQueue::where('transaction_id', 'MTN-DUP-001')->count());
    }

    public function test_manual_journal_requires_balance(): void
    {
        $response = $this->postJson('/api/v1/journal/manual', [
            'company_niu'     => 'P012345678901A',
            'posting_date'    => '2026-06-24',
            'reference_id'    => 'MAN-001',
            'source_pipeline' => 'MANUAL_CASH',
            'memo'            => 'Achat de fournitures',
            'lines'           => [
                ['account_code' => '601100', 'debit' => 50000, 'credit' => 0],
                ['account_code' => '571100', 'debit' => 0, 'credit' => 30000],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_prorata_vat_service_splits_correctly(): void
    {
        $company = Company::factory()->create([
            'vat_prorata_coefficient' => 80.00,
        ]);

        $service = app(\App\Services\ProrataVatService::class);
        $result  = $service->splitInputVat($company, '17500.00');

        $this->assertEquals('14000.00', $result['recoverable_vat']);
        $this->assertEquals('3500.00',  $result['non_recoverable_vat']);
    }

    public function test_fiscal_geography_router_activates_withholding_for_dge(): void
    {
        $company = Company::factory()->create(['tax_center' => 'DGE Douala']);
        $router  = app(\App\Services\FiscalGeographyRouter::class);

        $this->assertTrue($router->requiresSupplierWithholding($company));
        $this->assertEquals('0.055', $router->withholdingRate($company));
    }

    public function test_fiscal_geography_router_suppresses_withholding_for_cdi(): void
    {
        $company = Company::factory()->create(['tax_center' => 'CDI Bafoussam']);
        $router  = app(\App\Services\FiscalGeographyRouter::class);

        $this->assertFalse($router->requiresSupplierWithholding($company));
        $this->assertEquals('0.000', $router->withholdingRate($company));
    }
}
