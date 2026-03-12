<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientPortal\Http\Controllers\ClientPortalApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('client-portal/overview', [ClientPortalApiController::class, 'overview']);
});
