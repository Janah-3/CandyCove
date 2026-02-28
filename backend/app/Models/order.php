<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    //

    protected $fillable = [
        'user_id',
        'adress_id',
        'status',
        'ordered_at',
    ];


    function user(){
        return $this->belongsTo(User::class);
    }

    function address(){
        return $this->belongsTo(Address::class);
    }
    function products(){
        return $this->belongsToMany(product::class)->withPivot('quantity', 'price_at_order');
    }
}
