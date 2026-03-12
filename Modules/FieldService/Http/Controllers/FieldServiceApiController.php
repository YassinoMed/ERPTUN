<?php

namespace Modules\FieldService\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class FieldServiceApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'FieldService',
            'config' => config('fieldservice'),
        ], 'Field Service / interventions terrain overview fetched successfully.');
    }
}
