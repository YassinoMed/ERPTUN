<?php

namespace Modules\WorkflowDesigner\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class WorkflowDesignerApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'WorkflowDesigner',
            'config' => config('workflowdesigner'),
        ], 'Workflow designer visuel overview fetched successfully.');
    }
}
