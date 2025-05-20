<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreguntaSeccion;

class PreguntaController extends Controller
{
    /**
     * Endpoint público para preguntas frecuentes agrupadas por sección
     */
    public function index()
    {
        $secciones = PreguntaSeccion::with('preguntas')->get();

        return response()->json([
            'status' => true,
            'data' => $secciones
        ]);
    }
}
