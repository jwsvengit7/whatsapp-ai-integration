<?php


namespace App\Console\Commands;

use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class Prediction extends Command
{
    protected $signature = 'messages:send-prediction';
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
        Log::info("Message sent");
        $message= $this->whatsappService->cron_job_prediction();
        Log::info("Message ".$message);
    }


}
