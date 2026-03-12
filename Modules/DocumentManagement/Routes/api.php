<?php

use Illuminate\Support\Facades\Route;
use Modules\DocumentManagement\Http\Controllers\DocumentManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('document-management/overview', [DocumentManagementApiController::class, 'overview']);
});
