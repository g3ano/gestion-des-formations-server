<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\SearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('guest:sanctum')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/search', SearchController::class);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    require __DIR__ . '/v1/Formation.php';
    require __DIR__ . '/v1/Employee.php';
    require __DIR__ . '/v1/Action.php';
    require __DIR__ . '/v1/Participant.php';
});
