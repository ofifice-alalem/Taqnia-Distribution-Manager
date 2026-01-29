<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'free_quantity',
        'promotion_id',
        'unit_price',
        'total_price'
    ];

    public $timestamps = false;

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function promotion()
    {
        return $this->belongsTo(\App\Models\Promotion\ProductPromotion::class, 'promotion_id');
    }
}
