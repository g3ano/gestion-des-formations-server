<?php

use App\Http\Controllers\v1\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::delete('/', [EmployeeController::class, 'destroy']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
});
