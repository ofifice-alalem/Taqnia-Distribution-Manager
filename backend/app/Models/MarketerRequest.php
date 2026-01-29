<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarketerRequest extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'invoice_number',
        'marketer_id',
        'status'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }

    public function items()
    {
        return $this->hasMany(MarketerRequestItem::class, 'request_id');
    }

    public function statusDetail()
    {
        return $this->hasOne(MarketerRequestStatus::class, 'request_id');
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'في انتظار الموافقة',
            'approved' => 'انتظار التوثيق',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغى',
            'documented' => 'موثق',
            default => 'غير محدد'
        };
    }
}