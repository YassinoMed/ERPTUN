<?php

namespace Modules\IotTelemetry\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class IotTelemetryApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'IotTelemetry',
            'config' => config('iottelemetry'),
        ], 'IoT / capteurs / télémétrie overview fetched successfully.');
    }
}
