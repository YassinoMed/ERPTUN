<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiClientGatewayController;
use App\Http\Controllers\HotelApiController;
use App\Http\Controllers\AgriApiController;
use App\Http\Controllers\TenantInvoiceController;
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
    Route::post('clients/credentials', [ApiController::class, 'issueClientCredentials']);
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

Route::prefix('tenants/{tenant}')
    ->middleware(['auth:sanctum', 'tenant.access'])
    ->group(function () {
        Route::get('finance/invoices', [TenantInvoiceController::class, 'index'])
            ->middleware('scope:erp.finance.read');

        Route::get('finance/invoices/{invoice}', [TenantInvoiceController::class, 'show'])
            ->middleware('scope:erp.finance.read');

        Route::post('finance/invoices', [TenantInvoiceController::class, 'store'])
            ->middleware('scope:erp.finance.write');
    });

Route::prefix('client/v1')
    ->middleware(['api.client'])
    ->group(function () {
        Route::get('customers', [ApiClientGatewayController::class, 'customers']);
        Route::get('products', [ApiClientGatewayController::class, 'products']);
        Route::get('invoices', [ApiClientGatewayController::class, 'invoices']);
        Route::get('purchases', [ApiClientGatewayController::class, 'purchases']);
        Route::get('patients', [ApiClientGatewayController::class, 'patients']);
        Route::get('delivery-notes', [ApiClientGatewayController::class, 'deliveryNotes']);
    });
