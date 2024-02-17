<?php

namespace Database\Factories\v1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Action>
 */
class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateFin = fake()->dateTimeThisMonth(now()->addDays(20))->format('Y-m-d');
        $dateDebut = fake()->dateTimeThisMonth(now()->subDays(10))->format('Y-m-d');

        return [
            'formation_id' => fake()->numberBetween(1, 200),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'prevision' => fake()->sentence,
        ];
    }
}
