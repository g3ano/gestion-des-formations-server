<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\v1\CodeDomaine;
use App\Models\v1\Cout;
use App\Models\v1\Employee;
use App\Models\v1\Formation;
use App\Models\v1\Intitule;
use App\Models\v1\Organisme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('types')->insert([
            ['type' => 'formation/recrutement', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'perfectionnement', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'formation de reconversion', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'stages fournisseurs', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'formation induction', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'formation corporate', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('categories')->insert([
            ['categorie' => 'actions d\'adaptation au poste de travail', 'created_at' => now(), 'updated_at' => now()],
            ['categorie' => 'actions liées à l\'évolution des métiers & technologies', 'created_at' => now(), 'updated_at' => now()],
            ['categorie' => 'actions liées au développement des compétences', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('domaines')->insert([
            ['abbr' => 'fcm', 'domaine' => 'domaine fonction cœurs de métier', 'created_at' => now(), 'updated_at' => now()],
            ['abbr' => 'fst', 'domaine' => 'domaine fonction de soutien', 'created_at' => now(), 'updated_at' => now()],
            ['abbr' => 'fsp', 'domaine' => 'domaine fonction de support', 'created_at' => now(), 'updated_at' => now()],

        ]);

        Intitule::factory(100)->create();
        CodeDomaine::factory(100)->create();
        Organisme::factory(100)->create();
        Cout::factory(100)->create();
        Formation::factory(500)->create();

        Employee::factory(200)->create();
    }
}
