<?php

use Illuminate\Support\Facades\Route;
use Modules\Pos\Http\Controllers\PosApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('pos', [PosApiController::class, 'index']);
    Route::get('pos/{pos}', [PosApiController::class, 'show']);
});
