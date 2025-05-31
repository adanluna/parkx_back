@component('mail::message')
    # Hola {{ $user->name }},

    Hemos recibido un depósito en tu cuenta con los siguientes detalles:

    - **Monto:** ${{ number_format($amount, 2) }} MXN
    - **Método de Pago:** {{ ucfirst($paymentMethod) }}
    - **Fecha:** {{ \Carbon\Carbon::parse($date)->format('d/m/Y H:i') }}

    Gracias por confiar en nosotros.

    Saludos,<br>
    {{ config('app.name') }}
@endcomponent
