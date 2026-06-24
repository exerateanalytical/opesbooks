<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SyscohadaAccount;
use App\Services\JournalPostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class JournalPostingTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

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
    }

    public function test_balanced_entry_posts_successfully(): void
    {
        $poster = app(JournalPostingService::class);

        $entry = $poster->post([
            'company_id'      => $this->company->id,
            'posting_date'    => '2026-06-24',
            'reference_id'    => 'TEST-001',
            'source_pipeline' => 'MANUAL_CASH',
            'memo'            => 'Test balanced entry',
            'status'          => 'POSTED',
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
            'company_id'      => $this->company->id,
            'posting_date'    => '2026-06-24',
            'reference_id'    => 'TEST-002',
            'source_pipeline' => 'MANUAL_CASH',
            'memo'            => 'Unbalanced test',
            'status'          => 'POSTED',
        ], [
            ['account_code' => '571100', 'debit' => '50000', 'credit' => '0'],
            ['account_code' => '701100', 'debit' => '0', 'credit' => '40000'], // Off by 10000
        ]);
    }

    public function test_momo_revenue_callback_posts_correct_accounts(): void
    {
        $response = $this->postJson('/api/v1/ingest/telecom/callback', [
            'company_niu'    => 'P012345678901A',
            'operator'       => 'MTN',
            'transaction_id' => 'MTN-TXN-001',
            'amount'         => 119250,
            'message'        => 'Transfert recu de Client A',
            'date'           => '2026-06-24',
        ]);

        $response->assertCreated();

        // Wallet debited
        $mtnAccount = SyscohadaAccount::where('code', '571200')->first();
        $this->assertDatabaseHas('journal_lines', [
            'syscohada_account_id' => $mtnAccount->id,
        ]);

        // Revenue credited
        $revenueAccount = SyscohadaAccount::where('code', '701100')->first();
        $this->assertDatabaseHas('journal_lines', [
            'syscohada_account_id' => $revenueAccount->id,
        ]);
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
                ['account_code' => '571100', 'debit' => 0, 'credit' => 30000], // Imbalanced
            ],
        ]);

        $response->assertStatus(422);
    }
}
