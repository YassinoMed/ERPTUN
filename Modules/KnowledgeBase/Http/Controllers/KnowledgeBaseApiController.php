<?php

namespace Modules\KnowledgeBase\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class KnowledgeBaseApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'KnowledgeBase',
            'config' => config('knowledgebase'),
        ], 'Base de connaissances overview fetched successfully.');
    }
}
