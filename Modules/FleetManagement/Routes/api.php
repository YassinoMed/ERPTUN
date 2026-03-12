<?php

use Illuminate\Support\Facades\Route;
use Modules\FleetManagement\Http\Controllers\FleetManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('fleet-management/overview', [FleetManagementApiController::class, 'overview']);
});
