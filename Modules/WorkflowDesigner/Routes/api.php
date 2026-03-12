<?php

use Illuminate\Support\Facades\Route;
use Modules\WorkflowDesigner\Http\Controllers\WorkflowDesignerApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('workflow-designer/overview', [WorkflowDesignerApiController::class, 'overview']);
});
