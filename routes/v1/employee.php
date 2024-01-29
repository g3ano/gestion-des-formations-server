<?php

use App\Http\Controllers\v1\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::delete('/', [EmployeeController::class, 'delete']);
});
