<?php

use Illuminate\Support\Facades\Route;
use Modules\LegalManagement\Http\Controllers\LegalManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('legal-management/overview', [LegalManagementApiController::class, 'overview']);
});
