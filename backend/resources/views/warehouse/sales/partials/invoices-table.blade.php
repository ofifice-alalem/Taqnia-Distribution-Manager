<div class="modern-table">
    <table>
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>المسوق</th>
                <th>المتجر</th>
                <th>التاريخ</th>
                <th>المبلغ</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td><code>{{ $invoice->invoice_number }}</code></td>
                    <td>{{ $invoice->marketer->full_name }}</td>
                    <td><strong>{{ $invoice->store->name }}</strong></td>
                    <td>{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
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
                    <td colspan="7" class="empty-cell">
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
</style>
