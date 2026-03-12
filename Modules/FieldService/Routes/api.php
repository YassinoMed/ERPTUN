<?php

use Illuminate\Support\Facades\Route;
use Modules\FieldService\Http\Controllers\FieldServiceApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('field-service/overview', [FieldServiceApiController::class, 'overview']);
});
