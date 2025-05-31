<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = "transacciones";

    protected $guarded = [];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'id');
    }
}
