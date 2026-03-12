<?php

namespace Modules\ExpenseManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class ExpenseManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'ExpenseManagement',
            'config' => config('expensemanagement'),
        ], 'Notes de frais overview fetched successfully.');
    }
}
