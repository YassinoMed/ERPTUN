<?php

namespace Modules\DocumentManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class DocumentManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'DocumentManagement',
            'config' => config('documentmanagement'),
        ], 'GED / gestion documentaire overview fetched successfully.');
    }
}
