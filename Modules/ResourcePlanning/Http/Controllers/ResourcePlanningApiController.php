<?php

namespace Modules\ResourcePlanning\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class ResourcePlanningApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'ResourcePlanning',
            'config' => config('resourceplanning'),
        ], 'Planification avancée des ressources overview fetched successfully.');
    }
}
