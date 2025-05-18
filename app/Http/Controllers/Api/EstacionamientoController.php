<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estacionamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstacionamientoController extends Controller
{
    public function index(Request $request)
    {
        $query = Estacionamiento::with(['estado', 'municipio'])
            ->where('is_active', true);

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->filled('municipio_id')) {
            $query->where('municipio_id', $request->municipio_id);
        }

        return response()->json([
            'status' => true,
            'data' => $query->get()
        ]);
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:255',
            'longitud'     => 'required|numeric',
            'latitud'      => 'required|numeric',
            'is_active'    => 'boolean',
            'estado_id'    => 'required|exists:estados,id',
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $estacionamiento = Estacionamiento::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Estacionamiento creado correctamente',
            'data' => $estacionamiento,
        ]);
    }

    public function show($id)
    {
        $estacionamiento = Estacionamiento::with(['estado', 'municipio'])->find($id);

        if (! $estacionamiento) {
            return response()->json(['status' => false, 'message' => 'No encontrado'], 404);
        }

        return response()->json(['status' => true, 'data' => $estacionamiento]);
    }

    public function update(Request $request, $id)
    {
        $estacionamiento = Estacionamiento::find($id);

        if (! $estacionamiento) {
            return response()->json(['status' => false, 'message' => 'No encontrado'], 404);
        }

        $data = $request->validate([
            'nombre'       => 'sometimes|string|max:255',
            'longitud'     => 'sometimes|numeric',
            'latitud'      => 'sometimes|numeric',
            'is_active'    => 'sometimes|boolean',
            'estado_id'    => 'sometimes|exists:estados,id',
            'municipio_id' => 'sometimes|exists:municipios,id',
        ]);

        $estacionamiento->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Estacionamiento actualizado',
            'data' => $estacionamiento,
        ]);
    }

    public function destroy($id)
    {
        $estacionamiento = Estacionamiento::find($id);

        if (! $estacionamiento) {
            return response()->json(['status' => false, 'message' => 'No encontrado'], 404);
        }

        $estacionamiento->delete();

        return response()->json(['status' => true, 'message' => 'Estacionamiento eliminado']);
    }

    public function cercanos(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radio' => 'nullable|numeric',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radio = $request->radio ?? 5;

        $query = DB::table('estacionamientos')
            ->selectRaw("
            estacionamientos.*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitud)) *
                cos(radians(longitud) - radians(?)) +
                sin(radians(?)) * sin(radians(latitud))
            )) AS distancia", [$lat, $lng, $lat])
            ->where('is_active', true)
            ->having('distancia', '<=', $radio)
            ->orderBy('distancia');

        $result = $query->get();

        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }


    public function buscarPorNombre(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
        ]);

        $query = Estacionamiento::with(['estado', 'municipio'])
            ->where('is_active', true)
            ->where('nombre', 'LIKE', '%' . $request->nombre . '%');

        return response()->json([
            'status' => true,
            'data' => $query->get()
        ]);
    }
}
