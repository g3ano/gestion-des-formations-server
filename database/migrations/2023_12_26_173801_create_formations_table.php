<?php

use App\Models\v1\Categorie;
use App\Models\v1\CodeDomaine;
use App\Models\v1\Cout;
use App\Models\v1\Domaine;
use App\Models\v1\Intitule;
use App\Models\v1\Organisme;
use App\Models\v1\Type;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Categorie::class, 'categorie_id')->constrained('categories');
            $table->foreignIdFor(Domaine::class, 'domaine_id')->constrained('domaines');
            $table->foreignIdFor(Type::class, 'type_id')->constrained('types');
            $table->foreignIdFor(Intitule::class, 'intitule_id')->constrained('intitules');
            $table->foreignIdFor(Organisme::class, 'organisme_id')->constrained('organismes');
            $table->foreignIdFor(CodeDomaine::class, 'code_domaine_id')
                ->constrained('code_domaines');
            $table->foreignIdFor(Cout::class, 'cout_id')->constrained('couts');
            $table->string('structure', 50);
            $table->string('code_formation', 3);
            $table->string('mode', 50);
            $table->string('lieu', 50);
            $table->integer('effectif');
            $table->integer('durree');
            $table->integer('h_j');
            $table->string('observation', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
