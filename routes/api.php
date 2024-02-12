<?php

use App\Http\Controllers\v1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('guest:sanctum')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return [
                'user' => $request->user(),
            ];
        });
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    require __DIR__ . '/v1/formation.php';
    require __DIR__ . '/v1/employee.php';
    require __DIR__ . '/v1/action.php';
});
