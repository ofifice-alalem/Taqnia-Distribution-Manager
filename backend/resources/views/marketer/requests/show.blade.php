@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-file-earmark-text"></i> تفاصيل الطلب #{{ $request->id }} - 
                    <span class="badge fs-6
                        @if(!$request->status || $request->status->status == 'pending') bg-warning
                        @elseif($request->status->status == 'approved') bg-success
                        @else bg-danger
                        @endif">
                        @if(!$request->status || $request->status->status == 'pending') قيد المراجعة
                        @elseif($request->status->status == 'approved') موافق عليه
                        @else مرفوض
                        @endif
                    </span>
                </h4>
            </div>
            
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-person"></i> المسوق:</strong> {{ $request->marketer->full_name ?? 'غير محدد' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-calendar"></i> تاريخ الطلب:</strong> {{ $request->created_at }}</p>
                    </div>
                </div>

                @if($request->notes)
                    <div class="alert alert-info">
                        <strong><i class="bi bi-sticky"></i> ملاحظات:</strong> {{ $request->notes }}
                    </div>
                @endif

                <h5><i class="bi bi-boxes"></i> المنتجات المطلوبة</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية المطلوبة</th>
                                <th>الوحدة</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($request->items as $item)
                                @php 
                                    $itemTotal = $item->quantity * $item->product->current_price;
                                    $total += $itemTotal;
                                @endphp
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>قطعة</td>
                                    <td>{{ number_format($item->product->current_price, 2) }} ريال</td>
                                    <td>{{ number_format($item->quantity * $item->product->current_price, 2) }} ريال</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="4">الإجمالي</th>
                                <th>{{ number_format($total, 2) }} ريال</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear"></i> الإجراءات</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('marketer.requests.index') }}" class="btn btn-secondary w-100 mb-2">
                    <i class="bi bi-arrow-right"></i> رجوع للقائمة
                </a>
                
                @if(!$request->status || $request->status->status == 'pending')
                    <button type="button" class="btn btn-danger w-100" onclick="cancelRequest({{ $request->id }})">
                        <i class="bi bi-x-circle"></i> إلغاء الطلب
                    </button>
                @endif
            </div>
        </div>
        
        @if($request->status && $request->status->status == 'approved')
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h6><i class="bi bi-info-circle"></i> معلومات الموافقة</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>تمت الموافقة في:</strong></p>
                    <p>{{ $request->status->created_at ?? now() }}</p>
                    <p class="mb-1"><strong>أمين المخزن:</strong></p>
                    <p>{{ $request->status->keeper->full_name ?? 'غير محدد' }}</p>
                    <div class="alert alert-success">
                        <small><i class="bi bi-check-circle"></i> تم نقل البضاعة إلى المخزن المحجوز</small>
                    </div>
                </div>
            </div>
        @endif
        
        @if($documentInfo)
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h6><i class="bi bi-image"></i> معلومات التوثيق</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>تم التوثيق في:</strong> {{ $documentInfo->confirmed_at }}</p>
                    <p class="mb-3"><strong>بواسطة:</strong> أمين المخزن</p>
                    
                    <div class="alert alert-info">
                        <small><i class="bi bi-check-circle"></i> تم نقل البضاعة إلى المخزن الفعلي</small>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function cancelRequest(id) {
    if(confirm('هل أنت متأكد من إلغاء هذا الطلب؟')) {
        window.location.href = `/marketer/requests/${id}/cancel`;
    }
}
</script>
@endsection