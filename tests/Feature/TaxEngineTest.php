<?php

namespace Tests\Feature;

use App\Services\CameroonTaxEngine;
use Tests\TestCase;

class TaxEngineTest extends TestCase
{
    public function test_vat_computation_from_ht(): void
    {
        $result = CameroonTaxEngine::compute('100000');

        $this->assertSame('100000.00', $result['amount_ht']);
        $this->assertSame('17500.00',  $result['base_vat']);   // 17.5%
        $this->assertSame('1750.00',   $result['cac']);         // 10% of VAT
        $this->assertSame('19250.00',  $result['total_tax']);   // 17500 + 1750
        $this->assertSame('119250.00', $result['amount_ttc']);  // 100000 + 19250
    }

    public function test_reverse_from_ttc(): void
    {
        $result = CameroonTaxEngine::reverseFromTtc('119250');

        // Should round-trip back to 100000 HT
        $this->assertSame('100000.00', $result['amount_ht']);
        $this->assertSame('119250.00', $result['amount_ttc']);
    }

    public function test_tax_endpoint_from_ht(): void
    {
        $response = $this->postJson('/api/v1/tax/from-ht', ['amount_ht' => 100000]);

        $response->assertOk()
            ->assertJsonFragment(['amount_ht' => '100000.00'])
            ->assertJsonFragment(['amount_ttc' => '119250.00'])
            ->assertJsonFragment(['base_vat' => '17500.00'])
            ->assertJsonFragment(['cac' => '1750.00']);
    }

    public function test_tax_endpoint_from_ttc(): void
    {
        $response = $this->postJson('/api/v1/tax/from-ttc', ['amount_ttc' => 119250]);

        $response->assertOk()
            ->assertJsonFragment(['amount_ht' => '100000.00']);
    }
}
