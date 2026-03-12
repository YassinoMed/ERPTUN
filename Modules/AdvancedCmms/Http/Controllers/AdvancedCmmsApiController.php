<?php

namespace Modules\AdvancedCmms\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class AdvancedCmmsApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'AdvancedCmms',
            'config' => config('advancedcmms'),
        ], 'CMMS / maintenance avancée overview fetched successfully.');
    }
}
