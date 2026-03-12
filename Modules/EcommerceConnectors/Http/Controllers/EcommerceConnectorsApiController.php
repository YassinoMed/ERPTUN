<?php

namespace Modules\EcommerceConnectors\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class EcommerceConnectorsApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'EcommerceConnectors',
            'config' => config('ecommerceconnectors'),
        ], 'Connecteurs e-commerce overview fetched successfully.');
    }
}
