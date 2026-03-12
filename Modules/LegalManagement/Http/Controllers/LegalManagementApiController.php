<?php

namespace Modules\LegalManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class LegalManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'LegalManagement',
            'config' => config('legalmanagement'),
        ], 'Module juridique overview fetched successfully.');
    }
}
