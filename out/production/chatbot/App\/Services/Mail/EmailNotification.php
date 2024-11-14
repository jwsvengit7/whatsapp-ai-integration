<?php

namespace App\Services\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class EmailNotification extends Mailable
    {
        use Queueable, SerializesModels;

    public int $otp;
    public string $email;
    public string $userName;

    public function __construct($otp,$email,$userName)
    {
        $this->otp = $otp;
        $this->email = $email;
        $this->userName = $userName;
    }

        public function build(): EmailNotification
        {
            return $this->subject('WhatsApp AI OTP')
                ->view('emails')
                ->with(['otp' => $this->otp,"email"=>$this->email,"userName"=>$this->userName]);
        }

}
