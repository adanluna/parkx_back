<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaccion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransaccionController extends Controller
{
    static function addTransaccion(int $user_id, float $monto, string $metodo_pago, string $tarjeta, string $tipo, $estacionamiento_id)
    {
        // Validar los parámetros
        if (!in_array($tipo, ['abono', 'retiro'])) {
            throw new \InvalidArgumentException('Tipo de transacción inválido. Debe ser "abono" o "retiro".');
        }

        // Crear la transacción
        Transaccion::create([
            'user_id' => $user_id,
            'monto' => $monto,
            'metodo_pago' => $metodo_pago,
            'tarjeta' => $tarjeta,
            'tipo' => $tipo,
            'estacionamiento_id' => $estacionamiento_id
        ]);
    }
}
