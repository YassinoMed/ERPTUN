<?php

namespace Modules\SlaManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class SlaManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'SlaManagement',
            'config' => config('slamanagement'),
        ], 'Gestion des SLA overview fetched successfully.');
    }
}
