<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AppUser extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'app_users';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    public function scopeActivos($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
