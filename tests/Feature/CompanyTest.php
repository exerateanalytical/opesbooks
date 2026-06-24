<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Database\Seeders\SyscohadaAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SyscohadaAccountSeeder::class);

        // Create a seed company + owner to get an auth token for all tests
        $company     = Company::factory()->create();
        $owner       = User::factory()->create(['company_id' => $company->id, 'role' => 'OWNER']);
        $this->token = $owner->createToken('test')->plainTextToken;
    }

    public function test_can_create_company(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/companies', [
            'name'       => 'Camtech SARL',
            'niu'        => 'P012345678901A',
            'rccm'       => 'RC/DLA/2023/B/1234',
            'tax_regime' => 'REEL',
            'tax_center' => 'CIME Douala I',
            'phone'      => '+237600000000',
            'email'      => 'contact@camtech.cm',
        ]);

        $response->assertCreated()->assertJsonFragment(['name' => 'Camtech SARL']);
        $this->assertDatabaseHas('companies', ['niu' => 'P012345678901A']);
    }

    public function test_niu_must_be_unique(): void
    {
        Company::factory()->create(['niu' => 'P012345678901A', 'rccm' => 'RC/DLA/2023/B/1234']);

        $response = $this->withToken($this->token)->postJson('/api/v1/companies', [
            'name'       => 'Another SARL',
            'niu'        => 'P012345678901A',
            'rccm'       => 'RC/DLA/2023/B/9999',
            'tax_regime' => 'SIMPLIFIE',
            'tax_center' => 'CSPL Yaoundé',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['niu']);
    }

    public function test_invalid_tax_regime_rejected(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/companies', [
            'name'       => 'Bad Regime',
            'niu'        => 'X999999',
            'rccm'       => 'RC/DLA/2024/B/0001',
            'tax_regime' => 'INVALID',
            'tax_center' => 'CIME',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['tax_regime']);
    }
}
