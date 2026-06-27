<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Database\Seeders\SyscohadaAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SyscohadaAccountSeeder::class);
    }

    public function test_register_creates_company_and_owner(): void
    {
        $res = $this->postJson('/api/v1/auth/register', [
            'company_name'       => 'SARL Test Douala',
            'company_niu'        => 'M0820000TEST01',
            'company_rccm'       => 'RC/DLA/2026/B/TEST01',
            'company_tax_regime' => 'REEL',
            'company_tax_center' => 'CIME Douala I',
            'name'               => 'Jean Kamga',
            'email'              => 'jean@test.cm',
            'password'           => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $res->assertStatus(201)
            ->assertJsonStructure(['token', 'user', 'company']);

        $this->assertDatabaseHas('companies', ['niu' => 'M0820000TEST01']);
        $this->assertDatabaseHas('users', ['email' => 'jean@test.cm', 'role' => 'OWNER']);
    }

    public function test_login_returns_token(): void
    {
        $company = Company::factory()->create();
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'email'      => 'test@opes.cm',
            'password'   => bcrypt('secret123'),
            'role'       => 'OWNER',
        ]);

        $res = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@opes.cm',
            'password' => 'secret123',
        ]);

        $res->assertOk()->assertJsonStructure(['token', 'user']);
    }

    public function test_wrong_password_returns_validation_error(): void
    {
        $company = Company::factory()->create();
        User::factory()->create([
            'company_id' => $company->id,
            'email'      => 'bad@opes.cm',
            'password'   => bcrypt('correct'),
        ]);

        $this->postJson('/api/v1/auth/login', ['email' => 'bad@opes.cm', 'password' => 'wrong'])
            ->assertStatus(422);
    }

    public function test_me_endpoint_returns_user_and_fiscal_modules(): void
    {
        $company = Company::factory()->create(['tax_center' => 'CIME Douala I']);
        $user    = User::factory()->create(['company_id' => $company->id, 'role' => 'OWNER']);
        $token   = $user->createToken('test')->plainTextToken;

        $res = $this->withToken($token)->getJson('/api/v1/auth/me');

        $res->assertOk()
            ->assertJsonStructure(['user', 'company', 'fiscal_modules'])
            ->assertJsonPath('fiscal_modules.withholding_tax_active', true);
    }

    public function test_owner_can_invite_clerk(): void
    {
        // Permissive plan so the user-seat limit doesn't block the invite.
        \App\Models\PlanConfig::create([
            'name' => 'Free', 'slug' => 'free', 'price_xaf_monthly' => 0, 'price_xaf_yearly' => 0,
            'max_users' => -1, 'max_invoices_per_month' => -1, 'api_calls_per_hour' => -1,
            'features' => [], 'is_active' => true, 'sort_order' => 0,
        ]);

        $company = Company::factory()->create();
        $owner   = User::factory()->create(['company_id' => $company->id, 'role' => 'OWNER']);
        $token   = $owner->createToken('test')->plainTextToken;

        $res = $this->withToken($token)->postJson('/api/v1/auth/users', [
            'name'     => 'Caissier Paul',
            'email'    => 'paul@test.cm',
            'password' => 'Password123!',
            'role'     => 'CLERK',
        ]);

        $res->assertStatus(201)->assertJsonPath('user.role', 'CLERK');
    }

    public function test_clerk_cannot_invite_others(): void
    {
        $company = Company::factory()->create();
        $clerk   = User::factory()->create(['company_id' => $company->id, 'role' => 'CLERK']);
        $token   = $clerk->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/auth/users', [
            'name'     => 'Another',
            'email'    => 'another@test.cm',
            'password' => 'password123',
            'role'     => 'CLERK',
        ])->assertStatus(403);
    }

    public function test_suspended_company_gets_402(): void
    {
        $company = Company::factory()->create(['subscription_status' => 'SUSPENDED']);
        $user    = User::factory()->create(['company_id' => $company->id, 'role' => 'OWNER']);
        $token   = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertStatus(402);
    }

    public function test_logout_revokes_token(): void
    {
        $company = Company::factory()->create();
        $user    = User::factory()->create(['company_id' => $company->id]);
        $token   = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

        // Verify the token record was deleted from the DB
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
