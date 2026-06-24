<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

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
            'credit_limit_xaf'    => 5000000,
            'is_active'           => true,
        ];
    }
}
