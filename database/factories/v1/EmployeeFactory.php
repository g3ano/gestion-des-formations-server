<?php

namespace Database\Factories\v1;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sexe = Arr::random(['M', 'F']);
        $csp = Arr::random(['M', 'C']);

        return [
            'nom' => fake()->name,
            'prenom' => fake()->lastName,
            'localite' => fake()->city,
            'sexe' => $sexe,
            'direction' => fake()->word,
            'csp' => $csp,
            'date_naissance' => fake()->date,
            'lieu_naissance' => fake()->address,
            'email' => fake()->safeEmail,
            'matricule' => substr(str_replace('-', '', fake()->uuid), 0, 6),
        ];
    }
}
