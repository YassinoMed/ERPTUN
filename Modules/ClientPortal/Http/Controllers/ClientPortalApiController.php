<?php

namespace Modules\ClientPortal\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class ClientPortalApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'ClientPortal',
            'config' => config('clientportal'),
        ], 'Portail client avancé overview fetched successfully.');
    }
}
