<?php

namespace Modules\LoyaltyRewards\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class LoyaltyRewardsApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'LoyaltyRewards',
            'config' => config('loyaltyrewards'),
        ], 'Fidélité / cartes cadeaux overview fetched successfully.');
    }
}
