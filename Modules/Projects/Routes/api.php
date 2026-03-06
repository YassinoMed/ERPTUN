<?php

use Illuminate\Support\Facades\Route;
use Modules\Projects\Http\Controllers\ProjectsApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('get-projects', [ProjectsApiController::class, 'getProjects']);
    Route::post('add-tracker', [ProjectsApiController::class, 'addTracker']);
    Route::post('stop-tracker', [ProjectsApiController::class, 'stopTracker']);
    Route::post('upload-photos', [ProjectsApiController::class, 'uploadImage']);
});
