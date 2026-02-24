<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    //
    protected $fillable = [
        'product_id',
        'image_url',
    ];

    function product(){
        return $this->belongsTo(product::class);
    }
}
