<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estado;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::with(['estacionamientos' => function ($query) {
            $query->where('is_active', true)->with(['municipio']);
        }])->where('is_active', true)->get();

        $resultado = $estados->map(function ($estado) {
            return [
                'estado' => [
                    'id' => $estado->id,
                    'nombre' => $estado->nombre,
                    'is_active' => $estado->is_active,
                ],
                'estacionamientos' => $estado->estacionamientos
            ];
        });

        return response()->json([
            'status' => true,
            'estacionamiento' => $resultado
        ]);
    }
}
