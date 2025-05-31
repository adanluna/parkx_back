<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class WalletController extends Controller
{
    // Crear una nueva billetera
    public function store(Request $request)
    {
        $request->validate([
            'app_user_id' => 'required|exists:app_users,id',
            'balance' => 'required|numeric',
        ]);

        $wallet = Wallet::create($request->only('app_user_id', 'balance'));

        return response()->json($wallet, 201);
    }

    // Actualizar una billetera existente
    public function update(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        $request->validate([
            'balance' => 'required|numeric',
        ]);

        $wallet->update($request->only('balance'));

        return response()->json([
            'status'  => true,
            'data' => $wallet
        ], 200);
    }

    // Obtener una billetera por ID
    public function show($user_id)
    {
        $wallet = Wallet::where('user_id', $user_id)->first();
        if (!$wallet) {
            return response()->json(['message' => 'Billetera no encontrada'], 404);
        }

        return response()->json([
            'status'  => true,
            'data' => $wallet
        ], 200);
    }

    public function get(Request $request)
    {
        $stripe_key = env('STRIPE_SECRET_KEY');
        $stripe = new StripeClient($stripe_key);
        $wallet = Wallet::where('user_id', $request->user()->id)->first();
        $cards = $stripe->customers->allPaymentMethods($request->user()->stripe_id, ['type' => 'card',]);
        $wallet['cards'] = $cards['data'];
        return response()->json([
            'status'  => true,
            'data' => $wallet
        ], 200);
    }

    static function addFunds(int $user_id, String $amount): float
    {
        $wallet = Wallet::where('user_id', $user_id)->first();
        $wallet->increment('balance', floatval($amount));
        return $wallet->balance;
    }

    static function withdraw(int $user_id, String $amount): float
    {
        $wallet = Wallet::where('user_id', $user_id)->first();
        $wallet->decrement('balance', floatval($amount));
        return $wallet->balance;
    }
}
