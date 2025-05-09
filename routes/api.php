<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GoldRequestController;
use App\Http\Controllers\Api\V1\TradeController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('yaml-convertor', [Controller::class, 'yamlConvertor']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('gold_requests', GoldRequestController::class);
        Route::apiResource('trades', TradeController::class);
    });
});
