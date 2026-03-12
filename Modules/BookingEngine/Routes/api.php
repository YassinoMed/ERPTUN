<?php

use Illuminate\Support\Facades\Route;
use Modules\BookingEngine\Http\Controllers\BookingEngineApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('booking-engine/overview', [BookingEngineApiController::class, 'overview']);
});
