<?php

use Illuminate\Support\Facades\Route;
use Modules\FranchiseMultisite\Http\Controllers\FranchiseMultisiteApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('franchise-multisite/overview', [FranchiseMultisiteApiController::class, 'overview']);
});
