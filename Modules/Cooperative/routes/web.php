<?php

use Illuminate\Support\Facades\Route;
use Modules\Cooperative\Http\Controllers\CooperativeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('cooperative', CooperativeController::class)->names('cooperative');
});
