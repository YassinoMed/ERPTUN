<?php

use Illuminate\Support\Facades\Route;
use Modules\EcommerceConnectors\Http\Controllers\EcommerceConnectorsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('ecommerce-connectors/overview', [EcommerceConnectorsApiController::class, 'overview']);
});
