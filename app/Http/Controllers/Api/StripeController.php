<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class StripeController extends Controller
{
    public $stripe_key;
    public $stripe;
    public function __construct()
    {
        $this->stripe_key = env('STRIPE_SECRET_KEY');
        $this->stripe = new StripeClient($this->stripe_key);
    }
    public function createCustomer(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'email' => 'required|email',
        ]);

        $customer = $this->stripe->customers->create([
            'name' => $request->nombre,
            'email' =>  $request->email,
        ]);

        return response()->json([
            'status' => true,
            'data' => $customer
        ]);
    }
    public function createIntentCard(Request $request)
    {
        $user = $request->user();

        if (!$user->stripe_id) {
            return response()->json([
                'status' => false,
                'message' => 'El usuario no tiene un cliente de Stripe asociado.'
            ], 400);
        }

        try {
            $intent = $this->stripe->setupIntents->create([
                'payment_method_types' => ['card'],
                'customer' => $user->stripe_id
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getError()->message
            ], 402);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getError()->message
            ], 402);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => "Ocurrió un error al crear el intent."
            ], 500);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $intent->id,
                'clientSecret' => $intent->client_secret
            ]
        ]);
    }
    public function getCards(Request $request)
    {
        $cards = $this->stripe->customers->allPaymentMethods($request->user()->stripe_id, ['type' => 'card',]);
        return response()->json([
            'status' => true,
            'data' => $cards
        ]);
    }

    public function attachCard(Request $request)
    {
        $request->validate([
            'card' => 'required|string',
        ]);
        $card = $this->stripe->paymentMethods->attach(
            $request->card,
            ['customer' => $request->user()->stripe_id]
        );
        return response()->json([
            'status' => true,
            'data' => $card
        ]);
    }

    public function payment(Request $request)
    {
        $request->validate([
            'total' => 'required|string',
            'card' => 'required|string',
        ]);

        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount' => $request->total . '00',
                'currency' => 'mxn',
                'customer' => $request->user()->stripe_id,
                'payment_method_types' => ['card'],
                'description' => 'Prepago de $' . $request->total,
                'payment_method' => $request->card,
            ]);

            $confirm = $this->stripe->paymentIntents->confirm(
                $intent->id,
                [
                    'payment_method_types' => ['card'],
                    'payment_method' => $request->card,
                ]
            );
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getError()->message
            ], 402);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getError()->message
            ], 402);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => "Ocurrió un error procesando el pago: " . $e->getMessage()
            ], 402);
        }

        $balance = WalletController::addFunds($request->user()->id, $request->total);
        TransaccionController::addTransaccion($request->user()->id, $request->total, 'tarjeta', $request->card, 'abono', null);
        DB::table('wallets')->where('user_id', $request->user()->id)->update(['balance' => $balance]);
        DB::table('app_users')->where('id', $request->user()->id)->update(['balance' => $balance]);

        return response()->json([
            'status' => true,
            'data' => $confirm
        ]);
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $method = $request->method; // 'oxxo' o 'spei'

            if (!in_array($method, ['oxxo', 'spei'])) {
                return response()->json([
                    'status' => false,
                    'error' => 'Solo se permiten pagos con OXXO o SPEI.'
                ], 400);
            }

            $params = [
                'amount' => $request->amount . '00',
                'currency' => 'mxn',
                'customer' => $request->user()->stripe_id,
                'description' => 'Prepago de $' . $request->amount,
                'receipt_email' => $request->user()->email,
            ];

            if ($method === 'oxxo') {
                $params['payment_method_types'] = [$method];
                $params['payment_method_options'] = [
                    'oxxo' => [
                        'expires_after_days' => 2
                    ]
                ];
            } else {
                $params['payment_method_types'] = ['bank_transfer'];
                $params['payment_method_options'] = [
                    'bank_transfer' => [
                        'funding_type' => 'spei'
                    ],
                ];
            }

            $intent = $this->stripe->paymentIntents->create($params);

            return response()->json(data: [
                'client_secret' => $intent->client_secret,
                'status' => true,
                'data' => $intent
            ]);
        } catch (Exception $e) {
            Log::error('Error processing payment: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'error' => "Ocurrió un error procesando el pago: " . $e->getMessage()
            ], 402);
        }
    }



    public function deleteCard(Request $request)
    {
        $request->validate([
            'card' => 'required|string',
        ]);
        $card = $this->stripe->paymentMethods->detach($request->card);
        return response()->json([
            'status' => true,
            'data' => $card
        ]);
    }

    public function deleteCustomer(Request $request)
    {
        $this->stripe->customers->delete($request->user()->stripe_id);
        $request->user()->tokens()->delete();
        $request->user()->update(['is_active' => false, 'email' => $request->user()->email . '.deleted.' . $request->user()->id]);
        return response()->json([
            'status' => true
        ]);
    }
}
