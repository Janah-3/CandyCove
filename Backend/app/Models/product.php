<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class product extends Model
{
   
    protected $fillable = [
        'name',
        'price',
        'stock',
        'is_active',
        'category_id',
    ];



    function category(){
        return $this->belongsTo(category::class);
    }

   function orders(){
        return $this->belongsToMany(order::class)->withPivot('quantity', 'price_at_order');
    }

    function images(){
        return $this->hasMany(ProductImage::class);
    }

    protected static function booted()
{
    static::saved(function () {
        Cache::tags(['products'])->flush();
    });

    static::deleted(function () {
        Cache::tags(['products'])->flush();
    });
}


}
