<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaccion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function addPayment(Request $request)
    {
        $request->validate([
            'subtotal' => 'required',
            'descuento' => 'required',
            'total' => 'required',
            'estacionamiento_id' => 'required',
            'boleto' => 'required',
        ]);

        $balance = DB::table('wallets')->where('user_id', $request->user()->id)->value('balance');

        if ($balance < $request->total) {
            return response()->json([
                'status' => false,
                'message' => 'Saldo insuficiente'
            ], 422);
        }

        try {
            // Crear la transacción de retiro
            Transaccion::create([
                'user_id' => $request->user()->id,
                'tipo' => "retiro",
                'monto' => $request->total,
                'total' => $request->total,
                'comision' => $request->descuento ?? null,
                'subtotal' => $request->subtotal,
                'metodo_pago' => "App",
                'cupon_id' => $request->cupon_id ?? null,
                'descuento' => $request->descuento ?? 0,
                'estacionamiento_id' => $request->estacionamiento_id,
                'boleto' => $request->boleto
            ]);

            $balance = WalletController::withdraw($request->user()->id, $request->total);
            DB::table('app_users')->where('id', $request->user()->id)->update(['balance' => $balance]);


            return response()->json([
                'status' => true,
                'data' => [
                    'message' => 'Pago realizado con éxito',
                    'boleto' => $request->boleto
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al procesar el pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
