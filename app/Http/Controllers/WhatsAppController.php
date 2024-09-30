<?php

namespace App\Http\Controllers;

use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    /**
     * @var WhatsappService
     */
    private WhatsappService $whatsappService;
    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function receiveMessage(Request $request):string
    {
       return $this->whatsappService->receiveMessage($request);
    }

}
