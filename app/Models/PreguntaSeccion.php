<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreguntaSeccion extends Model
{
    protected $table = 'pregunta_secciones';
    protected $fillable = ['nombre'];

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class);
    }
}