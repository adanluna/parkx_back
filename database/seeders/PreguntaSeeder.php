<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pregunta;
use App\Models\PreguntaSeccion;

class PreguntaSeeder extends Seeder
{
    public function run(): void
    {
        $secciones = ['General', 'Técnico', 'Cuenta', 'Soporte'];

        foreach ($secciones as $nombre) {
            $seccion = PreguntaSeccion::create(['nombre' => $nombre]);

            Pregunta::create([
                'titulo' => '¿Pregunta 1 en ' . $nombre . '?',
                'texto' => 'Contenido de la primera pregunta en ' . $nombre,
                'pregunta_seccion_id' => $seccion->id,
            ]);

            Pregunta::create([
                'titulo' => '¿Pregunta 2 en ' . $nombre . '?',
                'texto' => 'Contenido de la segunda pregunta en ' . $nombre,
                'pregunta_seccion_id' => $seccion->id,
            ]);
        }
    }
}
