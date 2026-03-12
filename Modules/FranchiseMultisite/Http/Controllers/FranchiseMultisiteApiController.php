<?php

namespace Modules\FranchiseMultisite\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class FranchiseMultisiteApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'FranchiseMultisite',
            'config' => config('franchisemultisite'),
        ], 'Franchise / multi-site overview fetched successfully.');
    }
}
