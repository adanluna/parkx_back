<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';

    protected $guarded = [];
    protected function casts(): array
    {
        return [
            'valido_hasta' => 'datetime'
        ];
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return (new \DateTime($date->format('Y-m-d H:i:s'), $date->getTimezone()))
            ->setTimezone(new \DateTimeZone('America/Tijuana'))
            ->format('Y-m-d H:i:s');
    }


    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    public function estacionamiento()
    {
        return $this->belongsTo(Estacionamiento::class, 'estacionamiento_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
