<?php

use App\Http\Controllers\v1\FormationController;
use App\Http\Controllers\v1\FormationFormController;
use App\Http\Controllers\v1\FormController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->group(function () {
// });

Route::prefix('formations')->group(function () {
    Route::get('/', [FormationController::class, 'index']);
    Route::post('/', [FormationController::class, 'store']);
    Route::delete('/', [FormationController::class, 'delete']);
    Route::get('/commonValues', [FormationController::class, 'getCommonValues']);
    Route::get('/{id}', [FormationController::class, 'show']);
});
