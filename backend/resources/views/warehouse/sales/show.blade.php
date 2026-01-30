@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة')

@section('content')
<div class="row">
    <div class="col-md-4 order-md-2 order-first">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear"></i> الإجراءات</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('warehouse.sales.index') }}" class="btn btn-secondary w-100 mb-2">
                    <i class="bi bi-arrow-right"></i> رجوع للقائمة
                </a>
                
                @if($invoice->status == 'pending')
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#confirmModal">
                        <i class="bi bi-check-circle"></i> توثيق البيع
                    </button>
                @endif
            </div>
        </div>
        
        @if($invoice->status == 'approved' && $invoice->keeper)
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h6><i class="bi bi-check-circle"></i> معلومات التوثيق</h6>
                </div>
                <div class="card-body">
                    <p><strong>تم التوثيق في:</strong> {{ $invoice->confirmed_at }}</p>
                    <p class="mb-3"><strong>أمين المخزن:</strong> {{ $invoice->keeper->full_name }}</p>
                    
                    @if($invoice->stamped_invoice_image)
                        <button type="button" class="btn btn-info w-100" onclick="loadInvoiceImage()">
                            <i class="bi bi-image"></i> عرض الصورة الموثقة
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <div class="col-md-8 order-md-1 order-last">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-receipt"></i> فاتورة بيع - {{ $invoice->invoice_number }}
                    <span class="badge fs-6 @if($invoice->status == 'pending') bg-warning @else bg-success @endif">
                        @if($invoice->status == 'pending') في انتظار التوثيق @else موثق @endif
                    </span>
                </h4>
            </div>
            
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-person-badge"></i> المسوق:</strong> {{ $invoice->marketer->full_name }}</p>
                        <p><strong><i class="bi bi-shop"></i> المتجر:</strong> {{ $invoice->store->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-calendar"></i> التاريخ:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
                        @if($invoice->status == 'approved')
                            <p><strong><i class="bi bi-check-circle"></i> تم التوثيق:</strong> {{ $invoice->confirmed_at }}</p>
                        @endif
                    </div>
                </div>

                <h5><i class="bi bi-boxes"></i> المنتجات</h5>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>التخفيض</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity + $item->free_quantity }}</td>
                                    <td>
                                        @if($item->free_quantity > 0)
                                            <span class="badge bg-success">{{ $item->free_quantity }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->unit_price, 2) }} د.ع</td>
                                    <td>{{ number_format($item->quantity * $item->unit_price, 2) }} د.ع</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">المجموع الفرعي</th>
                                <th>{{ number_format($invoice->subtotal, 2) }} د.ع</th>
                            </tr>
                            @if($invoice->product_discount > 0)
                            <tr class="table-success">
                                <th colspan="4"><i class="bi bi-gift"></i> تخفيض المنتجات (هدايا)</th>
                                <th class="text-success">- {{ number_format($invoice->product_discount, 2) }} د.ع</th>
                            </tr>
                            @endif
                            @if($invoice->invoice_discount_amount > 0)
                            <tr class="table-info">
                                <th colspan="4">
                                    <i class="bi bi-percent"></i> تخفيض الفاتورة
                                    @if($invoice->invoice_discount_type == 'percentage')
                                        ({{ $invoice->invoice_discount_value }}%)
                                    @else
                                        (مبلغ ثابت)
                                    @endif
                                </th>
                                <th class="text-info">- {{ number_format($invoice->invoice_discount_amount, 2) }} د.ع</th>
                            </tr>
                            @endif
                            <tr class="table-dark">
                                <th colspan="4"><i class="bi bi-cash-stack"></i> الإجمالي النهائي</th>
                                <th>{{ number_format($invoice->total_amount, 2) }} د.ع</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($invoice->status == 'pending')
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('warehouse.sales.confirm', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="keeper_id" value="{{ Auth::id() }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">توثيق البيع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">صورة الفاتورة المختومة</label>
                        <input type="file" name="stamped_invoice_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">توثيق</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($invoice->stamped_invoice_image)
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">صورة الفاتورة المختومة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" style="background: #f8f9fa;">
                <img id="invoiceImage" src="" 
                     class="img-fluid" 
                     alt="صورة الفاتورة"
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

<script>
let imageLoaded = false;

function loadInvoiceImage() {
    if (!imageLoaded) {
        const invoiceImage = document.getElementById('invoiceImage');
        const imageLoader = document.getElementById('imageLoader');
        const openImageLink = document.getElementById('openImageLink');
        const downloadLink = document.getElementById('downloadLink');
        const imagePath = "{{ $invoice->stamped_invoice_image ? asset('storage/' . $invoice->stamped_invoice_image) : '' }}";
        
        invoiceImage.onload = function() {
            imageLoader.style.display = 'none';
            invoiceImage.style.display = 'block';
            openImageLink.style.display = 'inline-block';
            downloadLink.style.display = 'inline-block';
            imageLoaded = true;
        };
        
        invoiceImage.src = imagePath;
        openImageLink.href = imagePath;
        downloadLink.href = imagePath;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}
</script>
@endsection
