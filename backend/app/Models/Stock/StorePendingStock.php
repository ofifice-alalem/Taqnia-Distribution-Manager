<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class StorePendingStock extends Model
{
    protected $table = 'store_pending_stock';
    
    protected $fillable = [
        'store_id',
        'sales_invoice_id',
        'product_id',
        'quantity'
    ];

    public $timestamps = false;

    public function store()
    {
        return $this->belongsTo(\App\Models\Store\Store::class, 'store_id');
    }

    public function salesInvoice()
    {
        return $this->belongsTo(\App\Models\Sales\SalesInvoice::class, 'sales_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
}
