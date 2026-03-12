<?php

use Illuminate\Support\Facades\Route;
use Modules\ResourcePlanning\Http\Controllers\ResourcePlanningApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('resource-planning/overview', [ResourcePlanningApiController::class, 'overview']);
});
