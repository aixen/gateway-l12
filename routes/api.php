<?php

use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

Route::prefix('gateway')->middleware('gateway.api')->group(function () {
    Route::post('/login', [GatewayController::class, 'login']);
});
