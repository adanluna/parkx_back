@component('mail::message')
    # Hola {{ $user->name }},

    Hemos recibido un depósito en tu cuenta con los siguientes detalles:

    - Monto: ${{ number_format($amount, 2) }} MXN
    - Método de Pago:
    @switch($paymentMethod)
        @case('card')
            Tarjeta de crédito/débito
        @break

        @case('oxxo')
            Pago en OXXO
        @break

        @case('customer_balance')
            Transferencia Bancaria
        @break

        @default
            {{ ucfirst($paymentMethod) }}
    @endswitch
    - Fecha: {{ \Carbon\Carbon::parse($date)->format('d/m/Y H:i') }}

    Gracias por confiar en nosotros.

    Saludos,
    {{ config('app.name') }}
@endcomponent
