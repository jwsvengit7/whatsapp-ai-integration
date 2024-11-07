<?php

namespace App\Http\Controllers;

use App\Services\WhatsappService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

;

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
    public function handleWebhook(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|null
    {
        return $this->whatsappService->receiveMessage($request);
    }

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    public function handleOpenAI(Request $request): string
    {
        return $this->whatsappService->generateAIResponse($request);
    }

/**
 * @throws ConnectionException
 * @throws \Exception
 */
    public function sendUserMessage(Request $request): \Illuminate\Http\JsonResponse
    {

        return $this->whatsappService->sendUserMessage($request);
    }

    public function stopAiMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->whatsappService->stopAiMessage($request);
    }
    public function cron_job(): \Illuminate\Http\JsonResponse
    {
        return $this->whatsappService->cron_job();
    }


}
