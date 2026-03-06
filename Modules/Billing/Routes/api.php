<?php

use Illuminate\Support\Facades\Route;
use Modules\Billing\Http\Controllers\InvoiceApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('invoices', [InvoiceApiController::class, 'invoices']);
    Route::get('invoices/{invoice}', [InvoiceApiController::class, 'invoiceShow']);
});
