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
                <a href="{{ route('marketer.sales.index') }}" class="btn btn-secondary w-100 mb-2">
                    <i class="bi bi-arrow-right"></i> رجوع للقائمة
                </a>
                <a href="{{ route('marketer.sales.print', $invoice->id) }}" class="btn btn-primary w-100 mb-2" target="_blank">
                    <i class="bi bi-printer"></i> طباعة الفاتورة
                </a>
                @if($invoice->status == 'pending')
                    <button type="button" class="btn btn-danger w-100" onclick="cancelInvoice({{ $invoice->id }})">
                        <i class="bi bi-x-circle"></i> إلغاء الفاتورة
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
                    <p><strong>أمين المخزن:</strong> {{ $invoice->keeper->full_name }}</p>
                </div>
            </div>
        @endif
    </div>
    
    <div class="col-md-8 order-md-1 order-last">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-receipt"></i> فاتورة بيع - {{ $invoice->invoice_number }}
                    <span class="badge fs-6
                        @if($invoice->status == 'pending') bg-warning
                        @elseif($invoice->status == 'approved') bg-success
                        @else bg-secondary
                        @endif">
                        @if($invoice->status == 'pending') في انتظار التوثيق
                        @elseif($invoice->status == 'approved') موثق
                        @else ملغى
                        @endif
                    </span>
                </h4>
            </div>
            
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-shop"></i> المتجر:</strong> {{ $invoice->store->name }}</p>
                        <p><strong><i class="bi bi-person"></i> المالك:</strong> {{ $invoice->store->owner_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-calendar"></i> التاريخ:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong><i class="bi bi-person-badge"></i> المسوق:</strong> {{ $invoice->marketer->full_name }}</p>
                    </div>
                </div>

                <h5><i class="bi bi-boxes"></i> المنتجات</h5>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }} د.ع</td>
                                    <td>{{ number_format($item->total_price, 2) }} د.ع</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="3">الإجمالي</th>
                                <th>{{ number_format($invoice->total_amount, 2) }} د.ع</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelInvoice(id) {
    if(confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')) {
        window.location.href = `/marketer/sales/${id}/cancel`;
    }
}
</script>
@endsection
