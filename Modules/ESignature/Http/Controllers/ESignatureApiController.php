<?php

namespace Modules\ESignature\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class ESignatureApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'ESignature',
            'config' => config('esignature'),
        ], 'Signature électronique overview fetched successfully.');
    }
}
