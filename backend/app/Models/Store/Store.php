<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'phone',
        'address',
        'is_active'
    ];

    public function salesInvoices()
    {
        return $this->hasMany(\App\Models\Sales\SalesInvoice::class, 'store_id');
    }

    public function pendingStock()
    {
        return $this->hasMany(\App\Models\Stock\StorePendingStock::class, 'store_id');
    }

    public function actualStock()
    {
        return $this->hasMany(\App\Models\Stock\StoreActualStock::class, 'store_id');
    }
}
