<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\SalesApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('sales/quotations', [SalesApiController::class, 'quotations']);
    Route::get('sales/quotations/{quotation}', [SalesApiController::class, 'quotationShow']);
    Route::get('sales/proposals', [SalesApiController::class, 'proposals']);
    Route::get('sales/proposals/{proposal}', [SalesApiController::class, 'proposalShow']);
});
