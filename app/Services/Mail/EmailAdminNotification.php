<?php

namespace App\Services\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailAdminNotification
    extends Mailable
{
    use Queueable, SerializesModels;
    private User $user;
    private string $rawPassword;

    /**
     * @param $user
     */
    public function __construct($user,$rawPassword)
    {
        $this->user=$user;
        $this->rawPassword=$rawPassword;
    }


    public function build(): EmailAdminNotification
    {
        return $this->subject('WhatsApp AI Admin Creation')
            ->view('admin_creation')
            ->with(['password' => $this->rawPassword,"email"=>$this->user->email,"userName"=>$this->user->name]);
    }
}

