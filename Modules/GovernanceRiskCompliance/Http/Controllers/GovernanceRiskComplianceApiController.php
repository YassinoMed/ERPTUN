<?php

namespace Modules\GovernanceRiskCompliance\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class GovernanceRiskComplianceApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'GovernanceRiskCompliance',
            'config' => config('governanceriskcompliance'),
        ], 'GRC / conformité / audit / risques overview fetched successfully.');
    }
}
