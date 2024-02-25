<?php

use App\Http\Controllers\v1\ActionController;
use Illuminate\Support\Facades\Route;

Route::prefix('actions')->group(function () {
    Route::get('/', [ActionController::class, 'index']);
    Route::post('/', [ActionController::class, 'store']);
    Route::delete('/', [ActionController::class, 'destroy']);
    Route::put('/{id}', [ActionController::class, 'update']);
    Route::get('/{id}', [ActionController::class, 'show']);
});
