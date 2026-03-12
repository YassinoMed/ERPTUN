<?php

namespace Modules\AssetManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class AssetManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'AssetManagement',
            'config' => config('assetmanagement'),
        ], 'Gestion des immobilisations overview fetched successfully.');
    }
}
