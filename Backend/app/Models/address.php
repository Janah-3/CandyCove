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
        'building_no',
        'appartment_no',
        'phone',
    ];

    function user(){
        return $this->belongsTo(User::class);
    }

    function orders(){
        return $this->hasMany(order::class);
    }
}
