@extends('layouts.app')

@section('title', 'طلبات المسوقين')

@section('content')
<!-- المخزن الرئيسي -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center gap-2">
            <div class="icon-box bg-primary">
                <i class="bi bi-building"></i>
            </div>
            <h6 class="mb-0">المخزن الرئيسي</h6>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($mainStock as $stock)
                <div class="col-md-3 mb-3">
                    <div class="stock-item">
                        <div class="stock-info">
                            <div class="stock-name">{{ $stock->name }}</div>
                            <div class="stock-price">{{ number_format($stock->current_price, 2) }} ريال</div>
                        </div>
                        <div class="stock-quantity">{{ $stock->quantity }}</div>
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-list-ul"></i> طلبات المسوقين
    </h2>
</div>

<!-- جدول الطلبات -->
<div class="table-wrapper">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>المسوق</th>
                    <th>التاريخ</th>
                    <th>عدد المنتجات</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>
                            <div class="request-id">
                                <i class="bi bi-file-earmark-text"></i>
                                #{{ $request->id }}
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <i class="bi bi-person"></i>
                                {{ $request->marketer->full_name ?? 'غير محدد' }}
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <i class="bi bi-calendar3"></i>
                                {{ $request->created_at }}
                            </div>
                        </td>
                        <td>
                            <div class="items-count">
                                <i class="bi bi-boxes"></i>
                                {{ $request->items->count() }} منتج
                            </div>
                        </td>
                        <td>
                            <span class="status-badge 
                                @if(!$request->status || $request->status->status == 'pending') status-pending
                                @elseif($request->status->status == 'approved') status-approved
                                @else status-rejected
                                @endif">
                                {{ $request->status_text }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('warehouse.requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if(!$request->status || $request->status->status == 'pending')
                                    <button class="btn btn-sm btn-success" onclick="approveRequest({{ $request->id }})" title="موافقة">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectRequest({{ $request->id }})" title="رفض">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @elseif($request->status && $request->status->status == 'approved')
                                    @php
                                        $isDocumented = DB::table('delivery_confirmation')->where('request_id', $request->id)->exists();
                                    @endphp
                                    @if(!$isDocumented)
                                        <a href="{{ route('warehouse.requests.upload-document', $request->id) }}" class="btn btn-sm btn-warning" title="توثيق">
                                            <i class="bi bi-camera"></i>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>لا توجد طلبات حالياً</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.icon-box {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1em;
}

.stock-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    transition: var(--transition);
}

.stock-item:hover {
    border-color: var(--primary);
    background: var(--primary-light);
}

.stock-info {
    flex: 1;
}

.stock-name {
    font-weight: 600;
    color: var(--text-heading);
    margin-bottom: 2px;
}

.stock-price {
    font-size: 0.85em;
    color: var(--text-muted);
}

.stock-quantity {
    background: var(--primary);
    color: white;
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-weight: 700;
    font-size: 0.9em;
}

.page-title {
    color: var(--text-heading);
    font-weight: 700;
    font-size: 1.5em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-wrapper {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.request-id {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--text-heading);
}

.user-info, .date-info, .items-count {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-main);
}

.status-badge {
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 0.85em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.status-approved {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.action-buttons {
    display: flex;
    gap: 6px;
}

.action-buttons .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-state {
    color: var(--text-muted);
    text-align: center;
}

.empty-state i {
    font-size: 3em;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 1.1em;
}
</style>

<script>
function approveRequest(id) {
    if(confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/warehouse/requests/${id}/approve`;
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
        form.action = `/warehouse/requests/${id}/reject`;
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