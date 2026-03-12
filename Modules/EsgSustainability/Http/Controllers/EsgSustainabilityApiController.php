<?php

namespace Modules\EsgSustainability\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class EsgSustainabilityApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'EsgSustainability',
            'config' => config('esgsustainability'),
        ], 'ESG / carbone / durabilité overview fetched successfully.');
    }
}
