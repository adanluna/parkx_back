<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TransaccionController;
use App\Http\Controllers\Api\WalletController;
use App\Mail\PaymentReceived;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );

            // Procesa el evento según su tipo
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    // Monto en centavos (por ejemplo, 10000 = MX$100.00)
                    $amount = number_format(($paymentIntent->amount / 100), 2, '.', '');

                    // ID del cliente (si está asociado)
                    $customerId = $paymentIntent->customer;

                    // Fecha de creación en formato timestamp UNIX
                    //$created = $paymentIntent->created;

                    // ID del método de pago utilizado
                    $paymentMethod = $paymentIntent->payment_method_types[0]; // Por ejemplo, 'card', 'oxxo'

                    // Moneda utilizada (por ejemplo, 'mxn')
                    //$currency = $paymentIntent->currency;

                    // Descripción proporcionada al crear el PaymentIntent
                    //$description = $paymentIntent->description;

                    $balance = WalletController::addFundsStripeId($customerId, $amount);
                    TransaccionController::addTransaccion($request->user()->id, $request->total,  $paymentMethod, '', 'abono', null);
                    DB::table('wallets')->where('id', $request->user()->id)->update(['balance' => $balance]);

                    // Buscar al usuario asociado en tu base de datos
                    $user = AppUser::where('stripe_id', $customerId)->first();

                    if ($user) {
                        $amount = $paymentIntent->amount / 100; // Convertir de centavos a pesos
                        $paymentMethod = $paymentIntent->payment_method_types[0]; // Por ejemplo, 'card', 'oxxo'
                        $date = $paymentIntent->created;

                        // Enviar el correo
                        Mail::to($user->email)->send(new PaymentReceived($user, $amount, $paymentMethod, $date));
                    }
                    break;
                case 'payment_intent.failed':
                    $paymentIntent = $event->data->object;
                    // Lógica para manejar el fallo del pago
                    break;
                default:
                    Log::warning('Evento no manejado: ' . $event->type);
            }

            return response()->json(['status' => 'success']);
        } catch (SignatureVerificationException $e) {
            Log::error('Error de verificación de firma: ' . $e->getMessage());
            return response()->json(['error' => 'Firma inválida'], 400);
        }
    }
}
