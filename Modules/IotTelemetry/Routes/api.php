<?php

use Illuminate\Support\Facades\Route;
use Modules\IotTelemetry\Http\Controllers\IotTelemetryApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('iot-telemetry/overview', [IotTelemetryApiController::class, 'overview']);
});
