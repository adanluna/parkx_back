<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estacionamiento extends Model
{
    protected $fillable = [
        'nombre',
        'longitud',
        'latitud',
        'is_active',
        'estado_id',
        'municipio_id',
    ];

    protected $with = ['estado', 'municipio'];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }
}
