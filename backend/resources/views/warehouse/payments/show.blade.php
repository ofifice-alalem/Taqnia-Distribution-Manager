@extends('layouts.app')

@section('title', 'توثيق إيصال القبض')

@section('content')
<div class="mb-4">
    <h2 class="page-title">
        <i class="bi bi-cash-coin"></i> توثيق إيصال القبض
    </h2>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>تفاصيل الإيصال</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>رقم الإيصال:</strong> {{ $payment->payment_number }}
            </div>
            <div class="col-md-6">
                <strong>الحالة:</strong>
                @if($payment->status === 'pending')
                    <span class="badge bg-warning">في انتظار التوثيق</span>
                @elseif($payment->status === 'approved')
                    <span class="badge bg-success">موثق</span>
                @else
                    <span class="badge bg-danger">مرفوض</span>
                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <strong>المتجر:</strong> {{ $payment->store->name }}
            </div>
            <div class="col-md-6">
                <strong>المبلغ:</strong> {{ number_format($payment->amount, 2) }} ريال
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <strong>طريقة الدفع:</strong> 
                @if($payment->payment_method === 'cash')
                    <span class="badge bg-success">كاش</span>
                @elseif($payment->payment_method === 'transfer')
                    <span class="badge bg-info">حوالة</span>
                @else
                    <span class="badge bg-warning">شيك مصدق</span>
                @endif
            </div>
            <div class="col-md-6">
                <strong>تاريخ الإنشاء:</strong> {{ $payment->created_at }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <strong>المسوق:</strong> {{ $payment->marketer->full_name }}
            </div>
            <div class="col-md-6">
                <strong>تاريخ الإنشاء:</strong> {{ $payment->created_at }}
            </div>
        </div>

        @if($payment->status === 'approved')
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>أمين المخزن:</strong> {{ $payment->keeper->full_name }}
                </div>
                <div class="col-md-6">
                    <strong>تاريخ التوثيق:</strong> {{ $payment->confirmed_at }}
                </div>
            </div>

            @if($payment->receipt_image)
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>صورة الإيصال المختوم:</strong><br>
                        <img src="{{ asset('storage/' . $payment->receipt_image) }}" alt="Receipt" class="img-thumbnail mt-2" style="max-width: 400px;">
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

@if($payment->status === 'pending')
    <div class="card">
        <div class="card-header">
            <h5>توثيق الإيصال</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('warehouse.payments.confirm', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="receipt_image" class="form-label">صورة الإيصال المختوم <span class="text-danger">*</span></label>
                    <input type="file" name="receipt_image" id="receipt_image" 
                           class="form-control @error('receipt_image') is-invalid @enderror" 
                           accept="image/*" required>
                    @error('receipt_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">الحد الأقصى: 5 ميجابايت</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> توثيق وخصم الدين
                    </button>
                    <button type="button" class="btn btn-danger" onclick="rejectPayment()">
                        <i class="bi bi-x-circle"></i> رفض
                    </button>
                    <a href="{{ route('warehouse.payments.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right"></i> رجوع
                    </a>
                </div>
            </form>

            <form id="rejectForm" action="{{ route('warehouse.payments.reject', $payment->id) }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
@else
    <div class="mt-4">
        <a href="{{ route('warehouse.payments.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>
@endif

<script>
function rejectPayment() {
    if (confirm('هل أنت متأكد من رفض هذا الإيصال؟')) {
        document.getElementById('rejectForm').submit();
    }
}
</script>

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
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>
@endsection
