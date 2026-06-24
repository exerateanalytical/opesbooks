<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->company . ' SARL',
            'niu'        => 'P' . $this->faker->unique()->numerify('##########') . 'A',
            'rccm'       => 'RC/DLA/' . $this->faker->year . '/B/' . $this->faker->unique()->numerify('####'),
            'tax_regime' => $this->faker->randomElement(['REEL', 'SIMPLIFIE', 'LIBERATOIRE']),
            'tax_center' => $this->faker->randomElement(['CIME Douala I', 'CSPL Yaoundé', 'DGE Douala']),
            'phone'      => '+237' . $this->faker->numerify('#########'),
            'email'      => $this->faker->unique()->safeEmail,
            'address'    => $this->faker->address,
        ];
    }
}
