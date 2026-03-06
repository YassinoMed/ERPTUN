<?php

use Illuminate\Support\Facades\Route;
use Modules\Btp\Http\Controllers\BtpApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('btp/site-tracking/photos', [BtpApiController::class, 'sitePhotos']);
    Route::get('btp/subcontractors', [BtpApiController::class, 'subcontractors']);
    Route::get('btp/subcontract-invoices', [BtpApiController::class, 'subcontractInvoices']);
    Route::get('btp/subcontract-payments', [BtpApiController::class, 'subcontractPayments']);
    Route::get('btp/price-items', [BtpApiController::class, 'priceItems']);
    Route::get('btp/price-quotes', [BtpApiController::class, 'priceQuotes']);
    Route::get('btp/price-quote-items', [BtpApiController::class, 'priceQuoteItems']);
    Route::get('btp/equipments', [BtpApiController::class, 'equipments']);
    Route::get('btp/equipment-usages', [BtpApiController::class, 'equipmentUsages']);
    Route::get('btp/equipment-maintenances', [BtpApiController::class, 'equipmentMaintenances']);
});
