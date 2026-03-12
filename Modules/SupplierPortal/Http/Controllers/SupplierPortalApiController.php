<?php

namespace Modules\SupplierPortal\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class SupplierPortalApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'SupplierPortal',
            'config' => config('supplierportal'),
        ], 'Portail fournisseur avancé overview fetched successfully.');
    }
}
