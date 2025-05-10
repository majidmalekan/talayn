<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GoldRequestController;
use App\Http\Controllers\Api\V1\TradeController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('yaml-convertor', [AuthController::class, 'yamlConvertor']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('gold-requests', GoldRequestController::class);
        Route::apiResource('trades', TradeController::class)->only(['index', 'show']);
    });
});
