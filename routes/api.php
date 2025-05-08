<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GoldRequestController;
use App\Http\Controllers\Api\V1\TradeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
   Route::post('login', [AuthController::class,'login']);
   Route::middleware('auth:sanctum')->group(function () {
      Route::apiResource('gold_requests', GoldRequestController::class);
      Route::apiResource('trades', TradeController::class);
   });
});
