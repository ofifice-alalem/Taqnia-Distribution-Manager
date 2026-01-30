<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Model;

class InvoiceDiscountTier extends Model
{
    protected $fillable = [
        'min_amount',
        'discount_type',
        'discount_percentage',
        'discount_amount',
        'is_active',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
