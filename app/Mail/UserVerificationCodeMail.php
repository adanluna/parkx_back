<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserVerificationCodeMail extends Mailable
{
    public $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu código de verificación'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification-code'
        );
    }
}
