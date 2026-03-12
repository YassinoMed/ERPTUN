<?php

use Illuminate\Support\Facades\Route;
use Modules\ContractManagement\Http\Controllers\ContractManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('contract-management/overview', [ContractManagementApiController::class, 'overview']);
});
