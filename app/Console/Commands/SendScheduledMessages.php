<?php


namespace App\Console\Commands;

use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

class SendScheduledMessages extends Command
{
    protected $signature = 'messages:send-scheduled';
    protected $description = 'Send scheduled messages on their specified dates';
    private WhatsappService $whatsappService;

    public function __construct()
    {
        parent::__construct();
        $this->whatsappService=new WhatsappService();
    }

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $this->whatsappService->cron_job();

    }


}
