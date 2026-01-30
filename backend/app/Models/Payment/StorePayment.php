<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class StorePayment extends Model
{
    protected $fillable = [
        'payment_number',
        'store_id',
        'marketer_id',
        'keeper_id',
        'amount',
        'payment_method',
        'status',
        'receipt_image',
        'confirmed_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'confirmed_at' => 'datetime'
    ];

    public function store()
    {
        return $this->belongsTo(\App\Models\Store\Store::class, 'store_id');
    }

    public function marketer()
    {
        return $this->belongsTo(\App\Models\User::class, 'marketer_id');
    }

    public function keeper()
    {
        return $this->belongsTo(\App\Models\User::class, 'keeper_id');
    }
}
