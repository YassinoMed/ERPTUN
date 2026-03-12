<?php

use Illuminate\Support\Facades\Route;
use Modules\AssetManagement\Http\Controllers\AssetManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('asset-management/overview', [AssetManagementApiController::class, 'overview']);
});
