<?php

namespace App\Models\Withdrawal;

use Illuminate\Database\Eloquent\Model;

class MarketerWithdrawal extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'withdrawal_request_id',
        'marketer_id',
        'amount',
        'admin_id',
        'signed_receipt_image',
        'confirmed_at'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime'
    ];

    public function request()
    {
        return $this->belongsTo(MarketerWithdrawalRequest::class, 'withdrawal_request_id');
    }

    public function marketer()
    {
        return $this->belongsTo(\App\Models\User::class, 'marketer_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }
}
