<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    //
    protected $fillable = [
        'user_id',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    function user(){
        return $this->belongsTo(User::class);
    }

    function orders(){
        return $this->hasMany(order::class);
    }
}
