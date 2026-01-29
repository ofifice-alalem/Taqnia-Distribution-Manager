<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class MarketerActualStock extends Model
{
    protected $table = 'marketer_actual_stock';
    
    protected $fillable = [
        'marketer_id',
        'product_id',
        'quantity'
    ];

    public $timestamps = false;

    public function marketer()
    {
        return $this->belongsTo(\App\Models\User::class, 'marketer_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
}
