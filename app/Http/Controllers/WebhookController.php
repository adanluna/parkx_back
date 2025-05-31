<?php

namespace App\Http\Controllers;

use Stripe\Webhook;

use App\Http\Controllers\Api\TransaccionController;
use App\Http\Controllers\Api\WalletController;
use App\Mail\PaymentReceived;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;

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
                    Log::info($paymentIntent->customer);
                    $amount = number_format(($paymentIntent->amount / 100), 2, '.', '');
                    $customerId = $paymentIntent->customer;
                    $paymentMethod = $paymentIntent->payment_method_types[0];

                    // Buscar al usuario asociado en tu base de datos
                    $user = AppUser::where('stripe_id', $customerId)->first();

                    if ($user) {
                        $balance = WalletController::addFundsStripeId($customerId, $amount);
                        TransaccionController::addTransaccion($user->id, $amount, $paymentMethod, '', 'abono', null);
                        DB::table('wallets')->where('user_id', $user->id)->update(['balance' => $balance]);

                        $date = $paymentIntent->created;
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
