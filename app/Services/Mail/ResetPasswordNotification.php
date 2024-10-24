<?php

namespace App\Services\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordNotification
    extends Mailable
{
    use Queueable, SerializesModels;
    private User $user;

    /**
     * @param $user
     */
    public function __construct($user)
    {
        $this->user=$user;
    }

    public function build(): ResetPasswordNotification
    {
        return $this->subject('WhatsApp AI Admin Creation')
            ->view('reset')
            ->with(['link' => $this->user->remenber_token,"email"=>$this->user->email,"userName"=>$this->user->name]);
    }
}

