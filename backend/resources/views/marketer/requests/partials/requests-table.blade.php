<!-- Desktop Table -->
<div class="table-wrapper d-none d-md-block">
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
                                @if($type == 'pending') status-pending
                                @elseif($type == 'waiting-doc') status-approved
                                @elseif($type == 'documented') status-documented
                                @else status-rejected
                                @endif">
                                @if($type == 'pending') في انتظار الموافقة
                                @elseif($type == 'waiting-doc') في انتظار التوثيق
                                @elseif($type == 'documented') موثق
                                @else مرفوض
                                @endif
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('marketer.requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($type == 'pending')
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
                                <p>لا توجد طلبات في هذه الفئة</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards -->
<div class="requests-cards d-md-none">
    @forelse($requests as $request)
        <div class="request-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="bi bi-file-earmark-text"></i>
                    الطلب #{{ $request->id }}
                </div>
                <span class="status-badge 
                    @if($type == 'pending') status-pending
                    @elseif($type == 'waiting-doc') status-approved
                    @elseif($type == 'documented') status-documented
                    @else status-rejected
                    @endif">
                    @if($type == 'pending') في انتظار الموافقة
                    @elseif($type == 'waiting-doc') في انتظار التوثيق
                    @elseif($type == 'documented') موثق
                    @else مرفوض
                    @endif
                </span>
            </div>
            <div class="card-body">
                <div class="card-info">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ $request->created_at }}</span>
                </div>
                <div class="card-info">
                    <i class="bi bi-boxes"></i>
                    <span>{{ $request->items->count() }} منتج</span>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('marketer.requests.show', $request->id) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> عرض
                </a>
                @if($type == 'pending')
                    <button class="btn btn-sm btn-danger" onclick="cancelRequest({{ $request->id }})">
                        <i class="bi bi-x-circle"></i> إلغاء
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>لا توجد طلبات في هذه الفئة</p>
        </div>
    @endforelse
</div>

<style>
.requests-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.request-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-secondary);
}

.card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--text-heading);
    font-size: 0.95em;
}

.card-body {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.card-info {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-main);
    font-size: 0.9em;
}

.card-info i {
    color: var(--primary);
    width: 16px;
}

.card-footer {
    display: flex;
    gap: 8px;
    padding: 12px 16px;
    border-top: 1px solid var(--border);
}

.card-footer .btn {
    flex: 1;
    font-size: 0.85em;
}
</style>
