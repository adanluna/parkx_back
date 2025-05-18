<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estado;

class EstadoController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Estado::orderBy('nombre')->get()
        ]);
    }
}
