<?php

namespace App\Models\Withdrawal;

use Illuminate\Database\Eloquent\Model;

class MarketerWithdrawalRequest extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'marketer_id',
        'requested_amount',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function marketer()
    {
        return $this->belongsTo(\App\Models\User::class, 'marketer_id');
    }

    public function withdrawal()
    {
        return $this->hasOne(MarketerWithdrawal::class, 'withdrawal_request_id');
    }
}
