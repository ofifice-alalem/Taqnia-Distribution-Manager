@extends('layouts.app')

@section('title', 'طلباتي')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-list-ul"></i> طلباتي
    </h2>
    
    <a href="{{ route('marketer.requests.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> طلب جديد
    </a>
</div>

<!-- جدول الطلبات -->
<div class="table-wrapper">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
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
                                <a href="{{ route('marketer.requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if(!$request->status || $request->status->status == 'pending')
                                    <button class="btn btn-sm btn-danger" onclick="cancelRequest({{ $request->id }})" title="إلغاء الطلب">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
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

.date-info, .items-count {
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
function cancelRequest(id) {
    if(confirm('هل أنت متأكد من إلغاء هذا الطلب؟')) {
        window.location.href = `/marketer/requests/${id}/cancel`;
    }
}
</script>
@endsection