<?php

namespace Database\Factories\v1;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Formation>
 */
class FormationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $code_formation = Arr::random(['CDI', 'CDA', 'CDE', 'LDI', 'LDA', 'LDE']);
        $mode = Arr::random(['Distance', 'Présentiel', 'Blended']);
        $lieu = Arr::random(['E-LEARNING', 'ALGERIE', 'ALGER', 'LONDON', 'ORAN']);
        $effectif = fake()->numberBetween(1, 10);
        $durree = fake()->numberBetween(2, 10);
        $hJ = $effectif * $durree;

        return [
            'categorie_id' => fake()->numberBetween(1, 3),
            'domaine_id' => fake()->numberBetween(1, 3),
            'type_id' => fake()->numberBetween(1, 6),
            'intitule_id' => fake()->numberBetween(1, 99),
            'organisme_id' => fake()->numberBetween(1, 19),
            'code_domaine_id' => fake()->numberBetween(1, 19),
            'cout_id' => fake()->numberBetween(1, 19),
            'structure' => fake()->word(),
            'code_formation' => $code_formation,
            'mode' => $mode,
            'lieu' => $lieu,
            'effectif' => $effectif,
            'durree' => $durree,
            'h_j' => $hJ,
        ];
    }
}
