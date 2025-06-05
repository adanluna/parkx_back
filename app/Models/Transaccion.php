<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = "transacciones";

    protected $guarded = [];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return (new \DateTime($date->format('Y-m-d H:i:s'), $date->getTimezone()))
            ->setTimezone(new \DateTimeZone('America/Tijuana'))
            ->format('Y-m-d H:i:s');
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'id');
    }

    public function estacionamiento()
    {
        return $this->belongsTo(Estacionamiento::class, 'estacionamiento_id', 'id');
    }

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'cupon_id');
    }
}
