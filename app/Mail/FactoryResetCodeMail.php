<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FactoryResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $user;

    public function __construct($code, $user)
    {
        $this->code = $code;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject(__('messages.factory_reset_security_code'))
            ->view('emails.factory_reset_code');
    }
}
