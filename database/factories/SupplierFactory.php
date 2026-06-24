<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'company_id'          => null,
            'name'                => fake()->company(),
            'niu'                 => strtoupper(fake()->bothify('??########')),
            'email'               => fake()->unique()->companyEmail(),
            'phone'               => fake()->phoneNumber(),
            'address'             => fake()->address(),
            'payment_terms_days'  => 30,
            'is_active'           => true,
        ];
    }
}
