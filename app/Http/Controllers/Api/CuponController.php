<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\Request;

class CuponController extends Controller
{
    public function findCupon(Request $request)
    {
        $cupon = Cupon::whereRaw('UPPER(nombre) = UPPER(?)', [$request->codigo])
            ->where('estacionamiento_id', $request->estacionamiento_id)
            ->where(function ($query) {
                $query->whereNull('valido_hasta')
                    ->orWhere('valido_hasta', '>=', now()->toDateString());
            })
            ->first();

        if (!$cupon) {
            return response()->json([
                'status' => false,
                'message' => 'Cupon no encontrado'
            ], 422);
        }

        return response()->json([
            'status' => true,
            'data' => $cupon
        ]);
    }
}
