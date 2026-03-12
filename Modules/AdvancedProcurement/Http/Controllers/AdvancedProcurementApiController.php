<?php

namespace Modules\AdvancedProcurement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class AdvancedProcurementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'AdvancedProcurement',
            'config' => config('advancedprocurement'),
        ], 'Procurement avancé overview fetched successfully.');
    }
}
