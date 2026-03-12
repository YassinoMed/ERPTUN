<?php

use Illuminate\Support\Facades\Route;
use Modules\LearningManagement\Http\Controllers\LearningManagementApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('learning-management/overview', [LearningManagementApiController::class, 'overview']);
});
