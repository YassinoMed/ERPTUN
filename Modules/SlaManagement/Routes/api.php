<?php

use Illuminate\Support\Facades\Route;
use Modules\SlaManagement\Http\Controllers\SlaManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('sla-management/overview', [SlaManagementApiController::class, 'overview']);
});
