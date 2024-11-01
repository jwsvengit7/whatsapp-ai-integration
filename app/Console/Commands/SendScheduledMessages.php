<?php


namespace App\Console\Commands;

use App\Services\WhatsappService;
use Illuminate\Console\Command;
use App\Models\ScheduledMessage;
use App\Models\Customer;
use Carbon\Carbon;
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
        $today = Carbon::today()->toDateString();
        $scheduledMessages = ScheduledMessage::where('scheduled_date', $today)
            ->where('status', 'pending')
            ->get();

        foreach ($scheduledMessages as $scheduledMessage) {
            $cus = Customer::all();
            foreach ($cus as $customer) {
            if ($customer) {
                $this->whatsappService->sendMessage($customer->phone, $scheduledMessage->message_content, $customer->id, []);

                $scheduledMessage->status = 'sent';
                $scheduledMessage->save();

                $this->info("Message sent to {$customer->phone}");
            }
        }
        }
    }


}
