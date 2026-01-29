<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'marketer_id',
        'store_id',
        'total_amount',
        'status',
        'keeper_id',
        'stamped_invoice_image',
        'confirmed_at',
        'notes'
    ];

    public $timestamps = true;

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'invoice_id');
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
