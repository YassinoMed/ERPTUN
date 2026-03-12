<?php

use Illuminate\Support\Facades\Route;
use Modules\AdvancedProcurement\Http\Controllers\AdvancedProcurementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('advanced-procurement/overview', [AdvancedProcurementApiController::class, 'overview']);
});
