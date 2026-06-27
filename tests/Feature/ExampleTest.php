<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(\Database\Seeders\SyscohadaAccountSeeder::class);

        // '/' now serves the public marketing home page (200), not a redirect.
        $response = $this->get('/');

        $response->assertOk();
    }
}
