<?php

namespace Modules\RecurringBilling\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class RecurringBillingApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'RecurringBilling',
            'config' => config('recurringbilling'),
        ], 'Facturation récurrente overview fetched successfully.');
    }
}
