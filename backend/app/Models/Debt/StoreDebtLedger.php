<?php

namespace App\Models\Debt;

use Illuminate\Database\Eloquent\Model;

class StoreDebtLedger extends Model
{
    protected $table = 'store_debt_ledger';
    
    protected $fillable = [
        'store_id',
        'entry_type',
        'sales_invoice_id',
        'return_id',
        'payment_id',
        'amount'
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

    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment\StorePayment::class, 'payment_id');
    }
}
