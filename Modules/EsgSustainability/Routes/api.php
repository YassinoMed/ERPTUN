<?php

use Illuminate\Support\Facades\Route;
use Modules\EsgSustainability\Http\Controllers\EsgSustainabilityApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('esg-sustainability/overview', [EsgSustainabilityApiController::class, 'overview']);
});
