<?php

namespace Modules\BusinessIntelligence\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class BusinessIntelligenceApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'BusinessIntelligence',
            'config' => config('businessintelligence'),
        ], 'BI / tableaux de bord avancés overview fetched successfully.');
    }
}
