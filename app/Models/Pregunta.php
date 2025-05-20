<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pregunta extends Model
{
    protected $fillable = ['titulo', 'texto', 'pregunta_seccion_id'];

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(PreguntaSeccion::class, 'pregunta_seccion_id');
    }
}