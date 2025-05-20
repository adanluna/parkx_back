<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = ['nombre', 'is_active'];

    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    public function estacionamientos()
    {
        return $this->hasManyThrough(
            \App\Models\Estacionamiento::class,
            \App\Models\Municipio::class,
            'estado_id',        // Foreign key on municipios
            'municipio_id',     // Foreign key on estacionamientos
            'id',               // Local key on estados
            'id'                // Local key on municipios
        );
    }
}
