<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class StoreActualStock extends Model
{
    protected $table = 'store_actual_stock';
    
    protected $fillable = [
        'store_id',
        'product_id',
        'quantity'
    ];

    public $timestamps = false;

    public function store()
    {
        return $this->belongsTo(\App\Models\Store\Store::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
}
