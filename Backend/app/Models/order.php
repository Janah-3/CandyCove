<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    //
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'ordered_at',
        'total_amount',
        'number_of_items',
    ];


    function user(){
        return $this->belongsTo(User::class);
    }

    function address(){
        return $this->belongsTo(address::class);
    }
    function products(){
        return $this->belongsToMany(product::class)->withPivot('quantity', 'price_at_order');
    }
}
