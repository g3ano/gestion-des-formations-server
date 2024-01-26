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
        Schema::create('couts', function (Blueprint $table) {
            $table->id();
            $table->decimal('pedagogiques');
            $table->decimal('hebergement_restauration');
            $table->decimal('transport');
            $table->decimal('presalaire');
            $table->decimal('autres_charges');
            $table->decimal('dont_devise');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couts');
    }
};
