<?php

use Illuminate\Support\Facades\Route;
use Modules\Operations\Http\Controllers\OperationsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('operations/mrp/boms', [OperationsApiController::class, 'mrpBoms']);
    Route::get('operations/mrp/material-moves', [OperationsApiController::class, 'mrpMaterialMoves']);
    Route::get('operations/quality/checks', [OperationsApiController::class, 'qualityChecks']);
    Route::get('operations/maintenance/issues', [OperationsApiController::class, 'maintenanceIssues']);
    Route::get('operations/maintenance/equipment-maintenances', [OperationsApiController::class, 'maintenanceEquipment']);
});
