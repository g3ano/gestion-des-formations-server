<?php

use App\Http\Controllers\v1\ParticipantController;
use Illuminate\Support\Facades\Route;

Route::prefix('participants')->group(function () {
    Route::get('/', ParticipantController::class);
});
