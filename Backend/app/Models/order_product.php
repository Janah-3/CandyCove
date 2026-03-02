<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order_product extends Model
{
    //
    protected $table = 'order_product';
    
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_order',
    ];
    function order(){
        return $this->belongsTo(order::class);
    }
    function product(){
        return $this->belongsTo(product::class);
    }
}
