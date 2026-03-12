<?php

namespace Modules\LearningManagement\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class LearningManagementApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'LearningManagement',
            'config' => config('learningmanagement'),
        ], 'LMS / e-learning overview fetched successfully.');
    }
}
