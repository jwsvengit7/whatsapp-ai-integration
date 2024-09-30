<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Customer;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function receiveMessage(Request $request):string
    {
        $incomingMessage = $request->input('Body');
        $phoneNumber = $request->input('From');
        $exists = Customer::where('email', 'test@example.com')->exists();


        $customer = Customer::fi(
            ['phone' => $phoneNumber],
            ['phone'=>$phoneNumber,'name'=>'','email'=>'']
        );

        Conversation::created([
            'customer_id' => $customer->id,
            'message' => $incomingMessage,
            'is_from_customer' => true,
        ]);


       return $this->sendMessage($phoneNumber, $incomingMessage);
    }

    private function sendMessage($phoneNumber, $message):string
    {
        return "";

    }


}
