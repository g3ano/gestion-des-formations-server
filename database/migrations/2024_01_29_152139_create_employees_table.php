<?php

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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 50);
            $table->string('nom', 255);
            $table->string('prenom', 255);
            $table->string('localite', 50);
            $table->string('sexe', 1)->default('M');
            $table->string('direction', 50);
            $table->string('csp', 1);
            $table->timestamp('date_naissance');
            $table->string('lieu_naissance', 255);
            $table->string('email', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
