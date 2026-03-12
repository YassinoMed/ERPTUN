<?php

namespace Modules\EmployeeSelfService\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class EmployeeSelfServiceApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'EmployeeSelfService',
            'config' => config('employeeselfservice'),
        ], 'Portail employé self-service overview fetched successfully.');
    }
}
