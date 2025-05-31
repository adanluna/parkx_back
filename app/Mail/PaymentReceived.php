<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $amount;
    public $paymentMethod;
    public $date;

    public function __construct($user, $amount, $paymentMethod, $date)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->date = $date;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Confirmación de Pago Recibido',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment.received'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
