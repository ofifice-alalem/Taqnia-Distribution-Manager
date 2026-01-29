<div class="modern-table d-none d-md-block">
    <table>
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>المسوق</th>
                <th>المتجر</th>
                <th>التاريخ</th>
                <th>عدد الأصناف</th>
                <th>السعر</th>
                <th>التخفيض</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                @php
                    $totalPrice = $invoice->items->sum(function($item) {
                        return ($item->quantity + $item->free_quantity) * $item->unit_price;
                    });
                    $discount = $invoice->items->sum(function($item) {
                        return $item->free_quantity * $item->unit_price;
                    });
                @endphp
                <tr>
                    <td><code>{{ $invoice->invoice_number }}</code></td>
                    <td>{{ $invoice->marketer->full_name }}</td>
                    <td><strong>{{ $invoice->store->name }}</strong></td>
                    <td>{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $invoice->items->count() }}</td>
                    <td>{{ number_format($totalPrice, 2) }} د.ع</td>
                    <td>
                        @if($discount > 0)
                            <span class="badge bg-success">{{ number_format($discount, 2) }} د.ع</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><strong>{{ number_format($invoice->total_amount, 2) }} د.ع</strong></td>
                    <td>
                        <span class="badge-status @if($type == 'pending') badge-warning @else badge-success @endif">
                            @if($type == 'pending') في انتظار التوثيق @else موثق @endif
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('warehouse.sales.show', $invoice->id) }}" class="btn-action btn-view" title="عرض">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($type == 'pending')
                                <a href="{{ route('warehouse.sales.show', $invoice->id) }}" class="btn-action btn-confirm" title="توثيق">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty-cell">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>لا توجد فواتير</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
.modern-table {
    background: var(--bg-card);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.modern-table table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table thead {
    background: var(--bg-secondary);
}

.modern-table th {
    padding: 16px 20px;
    text-align: right;
    font-weight: 600;
    font-size: 14px;
    color: var(--text-muted);
    border-bottom: 2px solid var(--border);
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background-color 0.2s;
}

.modern-table tbody tr:hover {
    background-color: var(--bg-hover);
}

.modern-table td {
    padding: 16px 20px;
    text-align: right;
    font-size: 14px;
    color: var(--text-main);
}

.modern-table td code {
    background: #e9ecef;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-family: 'Courier New', monospace;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-view {
    background: #e7f3ff;
    color: #0066cc;
}

.btn-confirm {
    background: #d1f4e0;
    color: #0f5132;
}

.btn-confirm:hover {
    background: #b8eacc;
}

.badge-status {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-success {
    background: #d1e7dd;
    color: #0f5132;
}

.empty-cell {
    padding: 60px 20px !important;
}

.empty-state {
    text-align: center;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.requests-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.request-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
    font-size: 14px;
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
    font-size: 13px;
}

.card-info i {
    color: var(--text-muted);
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
    font-size: 13px;
}
</style>

<div class="requests-cards d-md-none">
    @forelse($invoices as $invoice)
        @php
            $totalPrice = $invoice->items->sum(function($item) {
                return ($item->quantity + $item->free_quantity) * $item->unit_price;
            });
            $discount = $invoice->items->sum(function($item) {
                return $item->free_quantity * $item->unit_price;
            });
        @endphp
        <div class="request-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="bi bi-receipt"></i>
                    {{ $invoice->invoice_number }}
                </div>
                <span class="badge-status @if($type == 'pending') badge-warning @else badge-success @endif">
                    @if($type == 'pending') في انتظار التوثيق @else موثق @endif
                </span>
            </div>
            <div class="card-body">
                <div class="card-info">
                    <i class="bi bi-person-badge"></i>
                    <span>{{ $invoice->marketer->full_name }}</span>
                </div>
                <div class="card-info">
                    <i class="bi bi-shop"></i>
                    <span>{{ $invoice->store->name }}</span>
                </div>
                <div class="card-info">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="card-info">
                    <i class="bi bi-boxes"></i>
                    <span>{{ $invoice->items->count() }} صنف</span>
                </div>
                <div class="card-info">
                    <i class="bi bi-tag"></i>
                    <span>السعر: {{ number_format($totalPrice, 2) }} د.ع</span>
                </div>
                @if($discount > 0)
                <div class="card-info">
                    <i class="bi bi-gift"></i>
                    <span>التخفيض: <span class="text-success">{{ number_format($discount, 2) }} د.ع</span></span>
                </div>
                @endif
                <div class="card-info">
                    <i class="bi bi-cash"></i>
                    <span><strong>الإجمالي: {{ number_format($invoice->total_amount, 2) }} د.ع</strong></span>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('warehouse.sales.show', $invoice->id) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> عرض
                </a>
                @if($type == 'pending')
                    <a href="{{ route('warehouse.sales.show', $invoice->id) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-check-circle"></i> توثيق
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>لا توجد فواتير</p>
        </div>
    @endforelse
</div>
