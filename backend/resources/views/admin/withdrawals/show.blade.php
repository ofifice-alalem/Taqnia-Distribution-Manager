@extends('layouts.app')

@section('title', 'تفاصيل طلب السحب')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-wallet2"></i> تفاصيل طلب السحب #{{ $request->id }}
    </h2>
    
    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right"></i> رجوع
    </a>
</div>

<div class="row">
    <!-- Request Details -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> معلومات الطلب</h5>
            </div>
            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">رقم الطلب:</span>
                    <span class="detail-value">#{{ $request->id }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">المسوق:</span>
                    <span class="detail-value">{{ $request->marketer->full_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">المبلغ المطلوب:</span>
                    <span class="detail-value fw-bold text-primary">{{ number_format($request->requested_amount, 2) }} ريال</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">تاريخ الطلب:</span>
                    <span class="detail-value">{{ $request->created_at->format('Y-m-d H:i') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">الحالة:</span>
                    <span class="detail-value">
                        @if($request->status === 'pending')
                            <span class="badge bg-warning">في الانتظار</span>
                        @elseif($request->status === 'approved')
                            <span class="badge bg-success">موافق عليه</span>
                        @else
                            <span class="badge bg-danger">مرفوض</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Marketer Balance -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cash-stack"></i> رصيد المسوق</h5>
            </div>
            <div class="card-body">
                <div class="balance-item total">
                    <div class="balance-label">إجمالي الأرباح</div>
                    <div class="balance-value">{{ number_format($totalEarned, 2) }} ريال</div>
                </div>
                
                <div class="balance-item withdrawn">
                    <div class="balance-label">المسحوب</div>
                    <div class="balance-value">{{ number_format($totalWithdrawn, 2) }} ريال</div>
                </div>
                
                <hr>
                
                <div class="balance-item available">
                    <div class="balance-label">الرصيد المتاح</div>
                    <div class="balance-value">{{ number_format($availableBalance, 2) }} ريال</div>
                </div>
                
                @if($request->requested_amount > $availableBalance)
                <div class="alert alert-danger mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    المبلغ المطلوب أكبر من الرصيد المتاح!
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
@if($request->status === 'pending')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-gear"></i> الإجراءات</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Approve Form -->
            <div class="col-md-6">
                <h6 class="mb-3">الموافقة وتسليم المبلغ</h6>
                <form action="{{ route('admin.withdrawals.approve', $request->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">صورة إيصال الاستلام المختوم <span class="text-danger">*</span></label>
                        <input type="file" name="signed_receipt_image" class="form-control" accept="image/*" required>
                        <small class="text-muted">يجب رفع صورة الإيصال الموقع من المسوق</small>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" @if($request->requested_amount > $availableBalance) disabled @endif>
                        <i class="bi bi-check-circle"></i> الموافقة وتسليم المبلغ
                    </button>
                </form>
            </div>
            
            <!-- Reject Form -->
            <div class="col-md-6">
                <h6 class="mb-3">رفض الطلب</h6>
                <form action="{{ route('admin.withdrawals.reject', $request->id) }}" method="POST">
                    @csrf
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        سيتم رفض الطلب ولن يتمكن المسوق من سحب هذا المبلغ
                    </div>
                    
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من رفض الطلب؟')">
                        <i class="bi bi-x-circle"></i> رفض الطلب
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Withdrawal Details (if approved) -->
@if($request->status === 'approved' && $request->withdrawal)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-check-circle"></i> تفاصيل السحب الموثق</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="detail-row">
                    <span class="detail-label">المبلغ المسحوب:</span>
                    <span class="detail-value fw-bold text-success">{{ number_format($request->withdrawal->amount, 2) }} ريال</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">تاريخ التوثيق:</span>
                    <span class="detail-value">{{ $request->withdrawal->confirmed_at->format('Y-m-d H:i') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">المسؤول:</span>
                    <span class="detail-value">{{ $request->withdrawal->admin->full_name }}</span>
                </div>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">إيصال الاستلام المختوم:</label>
                <div class="receipt-preview">
                    <img src="{{ asset('storage/' . $request->withdrawal->signed_receipt_image) }}" alt="Receipt" class="img-fluid">
                </div>
                <a href="{{ asset('storage/' . $request->withdrawal->signed_receipt_image) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2 w-100">
                    <i class="bi bi-download"></i> تحميل الصورة
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.page-title {
    color: var(--text-heading);
    font-weight: 700;
    font-size: 1.5em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-radius: 12px;
}

.card-header {
    background-color: var(--bg-secondary);
    border-bottom: 1px solid var(--border);
    padding: 15px 20px;
    border-radius: 12px 12px 0 0 !important;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: var(--text-secondary);
    font-weight: 500;
}

.detail-value {
    color: var(--text-heading);
    font-weight: 600;
}

.balance-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
}

.balance-item .balance-label {
    color: var(--text-secondary);
    font-weight: 500;
}

.balance-item .balance-value {
    font-weight: 700;
    font-size: 1.1em;
}

.balance-item.total .balance-value {
    color: #0d6efd;
}

.balance-item.withdrawn .balance-value {
    color: #dc3545;
}

.balance-item.available .balance-value {
    color: #198754;
    font-size: 1.3em;
}

.receipt-preview {
    border: 2px solid var(--border);
    border-radius: 8px;
    padding: 10px;
    background: var(--bg-secondary);
}

.receipt-preview img {
    border-radius: 4px;
    max-height: 300px;
    object-fit: contain;
    width: 100%;
}
</style>
@endsection
