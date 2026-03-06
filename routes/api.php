<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\HotelApiController;
use App\Http\Controllers\AgriApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [ApiController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [ApiController::class, 'logout']);
    Route::get('hotel/channels', [HotelApiController::class, 'channels']);
    Route::post('hotel/channels/sync', [HotelApiController::class, 'syncChannels']);
    Route::get('hotel/recommendations', [HotelApiController::class, 'recommendations']);
    Route::post('hotel/housekeeping/tasks', [HotelApiController::class, 'createHousekeepingTask']);
    Route::post('hotel/housekeeping/issues', [HotelApiController::class, 'createMaintenanceIssue']);
    Route::post('hotel/upsell/offers', [HotelApiController::class, 'generateUpsellOffer']);
    Route::post('hotel/upsell/convert', [HotelApiController::class, 'convertUpsell']);

    Route::get('agri/lots', [AgriApiController::class, 'lots']);
    Route::post('agri/lots', [AgriApiController::class, 'createLot']);
    Route::get('agri/lots/{lot}/events', [AgriApiController::class, 'traceEvents']);
    Route::post('agri/trace-events', [AgriApiController::class, 'createTraceEvent']);
    Route::post('agri/certificates', [AgriApiController::class, 'issueCertificate']);

    Route::get('agri/crop-plans', [AgriApiController::class, 'cropPlans']);
    Route::post('agri/crop-plans', [AgriApiController::class, 'createCropPlan']);

    Route::get('agri/cooperatives', [AgriApiController::class, 'cooperatives']);
    Route::post('agri/cooperatives/deliveries', [AgriApiController::class, 'createDelivery']);
    Route::post('agri/cooperatives/distributions', [AgriApiController::class, 'createDistribution']);

    Route::get('agri/contracts', [AgriApiController::class, 'contracts']);
    Route::post('agri/contracts/hedges', [AgriApiController::class, 'createHedge']);
    Route::post('agri/price-indices', [AgriApiController::class, 'createPriceIndex']);
});
