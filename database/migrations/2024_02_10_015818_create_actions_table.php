<?php

use App\Models\v1\Formation;
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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Formation::class, 'formation_id')
                ->constrained('formations')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('prevision', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
