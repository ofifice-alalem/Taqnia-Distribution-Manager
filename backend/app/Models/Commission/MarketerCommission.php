<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class MarketerCommission extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'payment_id',
        'marketer_id',
        'store_id',
        'keeper_id',
        'payment_amount',
        'commission_rate',
        'commission_amount',
        'confirmed_at'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime'
    ];

    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment\StorePayment::class, 'payment_id');
    }

    public function marketer()
    {
        return $this->belongsTo(\App\Models\User::class, 'marketer_id');
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\Store\Store::class, 'store_id');
    }

    public function keeper()
    {
        return $this->belongsTo(\App\Models\User::class, 'keeper_id');
    }
}
