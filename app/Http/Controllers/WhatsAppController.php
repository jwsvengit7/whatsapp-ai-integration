<?php

namespace App\Http\Controllers;

use App\Services\WhatsappService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class WhatsAppController extends BaseController
{
    /**
     * @var WhatsappService
     */
    private WhatsappService $whatsappService;
    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }


    /**
     * @throws \Exception
     */
    public function handleWebhook(Request $request)
    {
        return $this->whatsappService->receiveMessage($request);
    }

    /**
     * @throws ConnectionException
     */
    public function handleOpenAI(Request $request)
    {
        return $this->whatsappService->generateAIResponse($request);
    }

}
