<?php

use Illuminate\Support\Facades\Route;
use Modules\AdvancedCmms\Http\Controllers\AdvancedCmmsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('advanced-cmms/overview', [AdvancedCmmsApiController::class, 'overview']);
});
