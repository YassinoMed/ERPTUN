<?php

use Illuminate\Support\Facades\Route;
use Modules\ESignature\Http\Controllers\ESignatureApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('e-signature/overview', [ESignatureApiController::class, 'overview']);
});
