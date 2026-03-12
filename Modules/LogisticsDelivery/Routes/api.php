<?php

use Illuminate\Support\Facades\Route;
use Modules\LogisticsDelivery\Http\Controllers\LogisticsDeliveryApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('logistics-delivery/overview', [LogisticsDeliveryApiController::class, 'overview']);
});
