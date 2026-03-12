<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessIntelligence\Http\Controllers\BusinessIntelligenceApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('business-intelligence/overview', [BusinessIntelligenceApiController::class, 'overview']);
});
