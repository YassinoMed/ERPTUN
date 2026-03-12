<?php

namespace Modules\CustomerSubscriptions\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class CustomerSubscriptionsApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'CustomerSubscriptions',
            'config' => config('customersubscriptions'),
        ], 'Gestion des abonnements clients overview fetched successfully.');
    }
}
