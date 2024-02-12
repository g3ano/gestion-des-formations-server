<?php

use App\Models\v1\Action;
use App\Models\v1\Employee;
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
        Schema::create('action_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Action::class, 'action_id')
                ->constrained('actions')
                ->onDelete('cascade')
                ->onUpdate('restrict');
            $table->foreignIdFor(Employee::class, 'employee_id')
                ->constrained('employees')
                ->onDelete('cascade')
                ->onUpdate('restrict');
            $table->string('observation', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_employee');
    }
};
