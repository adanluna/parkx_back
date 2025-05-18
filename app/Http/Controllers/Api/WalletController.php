<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

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

        return response()->json($wallet);
    }

    // Obtener una billetera por ID
    public function show($user_id)
    {
        $wallet = Wallet::where('user_id', $user_id)->first();
        if (!$wallet) {
            return response()->json(['message' => 'Billetera no encontrada'], 404);
        }

        return response()->json($wallet);
    }
}