<?php

namespace Modules\BookingEngine\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class BookingEngineApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'BookingEngine',
            'config' => config('bookingengine'),
        ], 'Réservation / booking overview fetched successfully.');
    }
}
