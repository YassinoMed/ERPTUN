<?php

namespace Modules\WhatsAppOmnichannel\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;

class WhatsAppOmnichannelApiController extends Controller
{
    use ApiResponser;

    public function overview()
    {
        return $this->success([
            'module' => 'WhatsAppOmnichannel',
            'config' => config('whatsappomnichannel'),
        ], 'WhatsApp Business / omnicanal overview fetched successfully.');
    }
}
