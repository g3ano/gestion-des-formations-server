<?php

namespace Database\Factories\v1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Cout>
 */
class CoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pedagogiques' => fake()->randomFloat(2, 100.00, 10000.00),
            'hebergement_restauration' => fake()->randomFloat(2, 100.00, 10000.00),
            'transport' => fake()->randomFloat(2, 100.00, 10000.00),
            'presalaire' => fake()->randomFloat(2, 100.00, 10000.00),
            'autres_charges' => fake()->randomFloat(2, 100.00, 10000.00),
            'dont_devise' => fake()->randomFloat(2, 100.00, 10000.00),
        ];
    }
}
