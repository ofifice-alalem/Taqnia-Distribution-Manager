@extends('layouts.app')

@section('title', 'تفاصيل إيصال القبض')

@section('content')
<div class="row">
    <div class="col-md-4 order-md-2 order-first">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear"></i> الإجراءات</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('marketer.payments.index') }}" class="btn btn-secondary w-100 mb-2">
                    <i class="bi bi-arrow-right"></i> رجوع للقائمة
                </a>
                <a href="{{ route('marketer.payments.print', $payment->id) }}" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-printer"></i> طباعة الإيصال
                </a>
                @if($payment->status == 'pending')
                    <button type="button" class="btn btn-danger w-100" onclick="cancelPayment({{ $payment->id }})">
                        <i class="bi bi-x-circle"></i> إلغاء الإيصال
                    </button>
                @endif
            </div>
        </div>
        
        @if($payment->status == 'approved' && $payment->keeper)
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h6><i class="bi bi-check-circle"></i> معلومات التوثيق</h6>
                </div>
                <div class="card-body">
                    <p><strong>تم التوثيق في:</strong> {{ $payment->confirmed_at }}</p>
                    <p class="mb-3"><strong>أمين المخزن:</strong> {{ $payment->keeper->full_name }}</p>
                    
                    @if($payment->receipt_image)
                        <button type="button" class="btn btn-info w-100" onclick="loadReceiptImage()">
                            <i class="bi bi-image"></i> عرض الصورة الموثقة
                        </button>
                    @endif
                </div>
            </div>
            
            @if($payment->receipt_image)
            <div class="modal fade" id="imageModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">صورة الإيصال المختوم</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center" style="background: #f8f9fa;">
                            <img id="receiptImage" src="" 
                                 class="img-fluid" 
                                 alt="صورة الإيصال"
                                 style="max-height: 80vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none;">
                            <div id="imageLoader" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">جاري تحميل الصورة...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a id="downloadLink" href="" download class="btn btn-success" style="display: none;">
                                <i class="bi bi-download"></i> تحميل
                            </a>
                            <a id="openImageLink" href="" 
                               target="_blank" 
                               class="btn btn-primary"
                               style="display: none;">
                                <i class="bi bi-box-arrow-up-right"></i> فتح في نافذة جديدة
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif
    </div>
    
    <div class="col-md-8 order-md-1 order-last">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-cash-coin"></i> إيصال قبض - {{ $payment->payment_number }}
                    <span class="badge fs-6
                        @if($payment->status == 'pending') bg-warning
                        @elseif($payment->status == 'approved') bg-success
                        @else bg-secondary
                        @endif">
                        @if($payment->status == 'pending') في انتظار التوثيق
                        @elseif($payment->status == 'approved') موثق
                        @else ملغى
                        @endif
                    </span>
                </h4>
            </div>
            
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-shop"></i> المتجر:</strong> {{ $payment->store->name }}</p>
                        <p><strong><i class="bi bi-person"></i> المالك:</strong> {{ $payment->store->owner_name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-calendar"></i> التاريخ:</strong> {{ $payment->created_at }}</p>
                        <p><strong><i class="bi bi-person-badge"></i> المسوق:</strong> {{ $payment->marketer->full_name }}</p>
                    </div>
                </div>

                <h5><i class="bi bi-info-circle"></i> تفاصيل الدفع</h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%">المبلغ المدفوع</th>
                                <td>{{ number_format($payment->amount, 2) }} ريال</td>
                            </tr>
                            <tr>
                                <th>طريقة الدفع</th>
                                <td>
                                    @if($payment->payment_method === 'cash')
                                        <span class="badge bg-success">كاش</span>
                                    @elseif($payment->payment_method === 'transfer')
                                        <span class="badge bg-info">حوالة</span>
                                    @else
                                        <span class="badge bg-warning">شيك مصدق</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>تاريخ الإنشاء</th>
                                <td>{{ $payment->created_at }}</td>
                            </tr>
                            <tr>
                                <th>نسبة العمولة</th>
                                <td>
                                    @if($payment->status == 'approved' && $commission)
                                        <span class="badge bg-info">{{ $commission->commission_rate }}%</span>
                                    @else
                                        <span class="badge bg-info">{{ $currentRate }}%</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>الربح {{ $payment->status == 'approved' ? '' : 'المتوقع' }}</th>
                                <td>
                                    @if($payment->status == 'approved' && $commission)
                                        <span class="badge bg-success" style="font-size: 14px;">{{ number_format($commission->commission_amount, 2) }} ريال</span>
                                    @else
                                        <span class="badge bg-warning" style="font-size: 14px;">{{ number_format($expectedProfit, 2) }} ريال</span>
                                    @endif
                                </td>
                            </tr>
                            @if($payment->status == 'approved')
                                <tr>
                                    <th>تاريخ التوثيق</th>
                                    <td>{{ $payment->confirmed_at }}</td>
                                </tr>
                                <tr>
                                    <th>أمين المخزن</th>
                                    <td>{{ $payment->keeper->full_name }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let imageLoaded = false;

function loadReceiptImage() {
    if (!imageLoaded) {
        const receiptImage = document.getElementById('receiptImage');
        const imageLoader = document.getElementById('imageLoader');
        const openImageLink = document.getElementById('openImageLink');
        const downloadLink = document.getElementById('downloadLink');
        const imagePath = "{{ $payment->receipt_image ? asset('storage/' . $payment->receipt_image) : '' }}";
        
        receiptImage.onload = function() {
            imageLoader.style.display = 'none';
            receiptImage.style.display = 'block';
            openImageLink.style.display = 'inline-block';
            downloadLink.style.display = 'inline-block';
            imageLoaded = true;
        };
        
        receiptImage.src = imagePath;
        openImageLink.href = imagePath;
        downloadLink.href = imagePath;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

function cancelPayment(id) {
    if(confirm('هل أنت متأكد من إلغاء هذا الإيصال؟')) {
        window.location.href = `/marketer/payments/${id}/cancel`;
    }
}
</script>

<style>
.card {
    background: var(--bg-card);
    border: 1px solid var(--border);
}

.card-header {
    background: var(--bg-sidebar);
    border-bottom: 1px solid var(--border);
    color: var(--text-heading);
}

.card-header h4,
.card-header h5,
.card-header h6 {
    color: var(--text-heading);
    margin: 0;
}

.card-body p,
.card-body strong,
.card-body th,
.card-body td {
    color: var(--text-main);
}

.table {
    color: var(--text-main);
}

.table th {
    background: var(--bg-main);
    color: var(--text-heading);
    border-color: var(--border);
}

.table td {
    border-color: var(--border);
}

.bg-success.text-white h6 {
    color: white !important;
}

.modal-content {
    background: var(--bg-card);
    color: var(--text-main);
}

.modal-header {
    background: var(--bg-sidebar);
    border-bottom: 1px solid var(--border);
}

.modal-title {
    color: var(--text-heading);
}
</style>
@endsection
