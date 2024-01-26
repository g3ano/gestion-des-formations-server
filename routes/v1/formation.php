<?php

use App\Http\Controllers\v1\FormationController;
use App\Http\Controllers\v1\FormationFormController;
use App\Http\Controllers\v1\FormController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->group(function () {
// });

Route::prefix('formation')->group(function () {
    Route::get('/', [FormationController::class, 'index']);
    Route::post('/', [FormationController::class, 'store']);
    Route::delete('/', [FormationController::class, 'delete']);
    Route::post('/form/intitules', [FormationFormController::class, 'getIntitules']);

    // sort
    Route::get('/sort', [FormationController::class, 'sortColumn']);
});
