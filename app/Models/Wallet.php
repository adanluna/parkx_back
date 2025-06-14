<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Hidden;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $guarded = [];

    // Relations
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'id');
    }
}
