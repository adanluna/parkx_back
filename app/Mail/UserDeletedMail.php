<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserDeletedMail extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Gracias por haber sido parte de ParkX!'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-deleted'
        );
    }
}