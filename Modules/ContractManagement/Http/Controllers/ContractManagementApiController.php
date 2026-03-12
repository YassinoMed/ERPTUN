<?php

namespace Modules\ContractManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class ContractManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'ContractManagement',
            'config' => config('contractmanagement'),
        ], 'Gestion des contrats overview fetched successfully.');
    }
}
