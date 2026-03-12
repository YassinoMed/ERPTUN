<?php

namespace Modules\FleetManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class FleetManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'FleetManagement',
            'config' => config('fleetmanagement'),
        ], 'Gestion de flotte overview fetched successfully.');
    }
}
