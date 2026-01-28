@extends('layouts.app')

@section('title', 'الطلبات')

@section('content')
@if(Auth::user()->isSalesman())
    <!-- مخزون المسوق -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6><i class="fas fa-box"></i> المخزون الفعلي</h6>
                </div>
                <div class="card-body">
                    @forelse($actualStock as $stock)
                        <div class="d-flex justify-content-between">
                            <span>{{ $stock->name }}</span>
                            <span class="badge bg-success">{{ $stock->quantity }}</span>
                        </div>
                        <hr class="my-1">
                    @empty
                        <p class="text-muted mb-0">لا يوجد مخزون فعلي</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6><i class="fas fa-clock"></i> مخزون الحجز</h6>
                </div>
                <div class="card-body">
                    @forelse($reservedStock as $stock)
                        <div class="d-flex justify-content-between">
                            <span>{{ $stock->name }}</span>
                            <span class="badge bg-warning">{{ $stock->reserved_quantity }}</span>
                        </div>
                        <hr class="my-1">
                    @empty
                        <p class="text-muted mb-0">لا يوجد مخزون محجوز</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <!-- المخزن الرئيسي لأمين المخزن -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6><i class="fas fa-warehouse"></i> المخزن الرئيسي</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($mainStock as $stock)
                            <div class="col-md-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                    <div>
                                        <small class="text-muted">{{ $stock->name }}</small><br>
                                        <small>{{ number_format($stock->current_price, 2) }} ريال</small>
                                    </div>
                                    <span class="badge bg-primary fs-6">{{ $stock->quantity }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted mb-0">لا يوجد مخزون</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list"></i> الطلبات</h2>
    
    @if(Auth::user()->isSalesman())
        <a href="{{ route('requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> طلب جديد
        </a>
    @endif
</div>

<div class="row">
    @forelse($requests as $request)
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><strong>طلب #{{ $request->id }}</strong></span>
                    <span class="badge 
                        @if(!$request->status || $request->status->status == 'pending') bg-warning
                        @elseif($request->status->status == 'approved') bg-info
                        @else bg-danger
                        @endif">
                        {{ $request->status_text }}
                    </span>
                </div>
                
                <div class="card-body">
                    @if(Auth::user()->isSalesman())
                        <p class="mb-2">
                            <i class="fas fa-user"></i> 
                            <strong>أمين المخزن:</strong> أي أمين مخزن متاح
                        </p>
                    @else
                        <p class="mb-2">
                            <i class="fas fa-user"></i> 
                            <strong>المسوق:</strong> {{ $request->marketer->full_name ?? 'غير محدد' }}
                        </p>
                    @endif
                    
                    <p class="mb-2">
                        <i class="fas fa-calendar"></i> 
                        <strong>التاريخ:</strong> {{ $request->created_at }}
                    </p>
                    
                    <p class="mb-2">
                        <i class="fas fa-boxes"></i> 
                        <strong>عدد المنتجات:</strong> {{ $request->items->count() }}
                    </p>
                    
                    @if($request->notes)
                        <p class="mb-2">
                            <i class="fas fa-sticky-note"></i> 
                            <strong>ملاحظات:</strong> {{ Str::limit($request->notes, 50) }}
                        </p>
                    @endif
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('requests.show', $request->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye"></i> عرض التفاصيل
                    </a>
                    
                    @if(Auth::user()->isWarehouseKeeper() && (!$request->status || $request->status->status == 'pending'))
                        <button class="btn btn-success btn-sm" onclick="approveRequest({{ $request->id }})">
                            <i class="fas fa-check"></i> موافقة
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rejectRequest({{ $request->id }})">
                            <i class="fas fa-times"></i> رفض
                        </button>
                    @elseif(Auth::user()->isWarehouseKeeper() && $request->status && $request->status->status == 'approved')
                        @php
                            $isDocumented = DB::table('delivery_confirmation')->where('request_id', $request->id)->exists();
                        @endphp
                        @if(!$isDocumented)
                            <a href="{{ route('requests.upload-document', $request->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-camera"></i> توثيق
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> لا توجد طلبات حالياً
            </div>
        </div>
    @endforelse
</div>

<script>
function approveRequest(id) {
    if(confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/requests/${id}/approve`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PATCH">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectRequest(id) {
    const reason = prompt('اكتب سبب الرفض:');
    if(reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/requests/${id}/reject`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="rejection_reason" value="${reason}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection