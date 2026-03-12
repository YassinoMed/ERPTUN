<?php

use Illuminate\Support\Facades\Route;
use Modules\SupplierPortal\Http\Controllers\SupplierPortalApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('supplier-portal/overview', [SupplierPortalApiController::class, 'overview']);
});
