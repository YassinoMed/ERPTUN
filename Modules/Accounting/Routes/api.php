<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccountingApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('accounting/invoices', [AccountingApiController::class, 'invoices']);
    Route::get('accounting/invoices/{invoice}', [AccountingApiController::class, 'invoiceShow']);
    Route::get('accounting/bills', [AccountingApiController::class, 'bills']);
    Route::get('accounting/bills/{bill}', [AccountingApiController::class, 'billShow']);
    Route::get('accounting/expenses', [AccountingApiController::class, 'expenses']);
    Route::get('accounting/expenses/{expense}', [AccountingApiController::class, 'expenseShow']);
    Route::get('accounting/journals', [AccountingApiController::class, 'journals']);
    Route::get('accounting/journals/{journalEntry}', [AccountingApiController::class, 'journalShow']);
});
