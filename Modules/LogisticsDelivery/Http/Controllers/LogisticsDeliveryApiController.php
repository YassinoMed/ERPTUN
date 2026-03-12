<?php

namespace Modules\LogisticsDelivery\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class LogisticsDeliveryApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'LogisticsDelivery',
            'config' => config('logisticsdelivery'),
        ], 'Logistique / expédition / livraison overview fetched successfully.');
    }
}
