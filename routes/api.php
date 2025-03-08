<?php

use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

Route::prefix('gateway')->middleware('gateway.api')->group(function () {
    Route::post('/login', [GatewayController::class, 'login']);

    Route::middleware('verify.token')->group(function () {
        Route::post('/logout', [GatewayController::class, 'logout']);

        Route::post('/refresh', [GatewayController::class, 'refresh']);
    });
});
