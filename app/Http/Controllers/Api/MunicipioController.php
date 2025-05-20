<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Municipio;
use App\Models\Estacionamiento;

class MunicipioController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $municipio = Municipio::with('estado')->find($data['municipio_id']);

        return response()->json([
            'status' => true,
            'data' => $municipio
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'estado_id' => 'required|exists:estados,id',
        ]);

        $municipio = Municipio::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Municipio creado correctamente',
            'data'    => $municipio
        ]);
    }

    public function show($id)
    {
        $municipio = Municipio::with('estado')->find($id);

        if (! $municipio) {
            return response()->json(['status' => false, 'message' => 'No encontrado'], 404);
        }

        return response()->json(['status' => true, 'data' => $municipio]);
    }

    public function update(Request $request, $id)
    {
        $municipio = Municipio::find($id);

        if (! $municipio) {
            return response()->json(['status' => false, 'message' => 'No encontrado'], 404);
        }

        $data = $request->validate([
            'nombre'    => 'sometimes|string|max:255',
            'estado_id' => 'sometimes|exists:estados,id',
        ]);

        $municipio->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Municipio actualizado',
            'data'    => $municipio
        ]);
    }

    public function destroy($id)
    {
        $municipio = Municipio::find($id);

        if (! $municipio) {
            return response()->json(['status' => false, 'message' => 'No encontrado'], 404);
        }

        $municipio->delete();

        return response()->json(['status' => true, 'message' => 'Municipio eliminado']);
    }

    public function activeEstacionamientos()
    {
        $estacionamientos = Estacionamiento::where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'data' => $estacionamientos
        ]);
    }
}
