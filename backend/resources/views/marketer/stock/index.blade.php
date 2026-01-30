@extends('layouts.app')

@section('title', 'مخزوني')

@section('content')
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: #d1e7dd;">
                <i class="bi bi-box-seam" style="color: #0f5132;"></i>
            </div>
            <div class="stats-content">
                <h3>{{ number_format($actualStock->sum('quantity')) }}</h3>
                <p>إجمالي المخزون الفعلي</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: #fff3cd;">
                <i class="bi bi-hourglass-split" style="color: #856404;"></i>
            </div>
            <div class="stats-content">
                <h3>{{ number_format($reservedStock->sum('quantity')) }}</h3>
                <p>إجمالي المخزون المحجوز</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: #d1f4e0;">
                <i class="bi bi-cash-stack" style="color: #28a745;"></i>
            </div>
            <div class="stats-content">
                <h3>{{ number_format($actualStock->sum('total_value'), 2) }}</h3>
                <p>القيمة الإجمالية (دينار)</p>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#actual" type="button" role="tab">
            <i class="bi bi-box-seam"></i> المخزون الفعلي ({{ $actualStock->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reserved" type="button" role="tab">
            <i class="bi bi-hourglass-split"></i> المخزون المحجوز ({{ $reservedStock->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
            <i class="bi bi-clock-history"></i> فواتير بانتظار التوثيق ({{ $pendingInvoices->count() }})
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="actual">
        <div class="card">
            <div class="card-body">
                <!-- Desktop Table -->
                <div class="modern-table d-none d-md-block">
                    <table>
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>القيمة الإجمالية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actualStock as $item)
                            <tr>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td><span class="qty-badge qty-actual">{{ number_format($item->quantity) }}</span></td>
                                <td>{{ number_format($item->current_price, 2) }} دينار</td>
                                <td><strong>{{ number_format($item->total_value, 2) }} دينار</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-cell">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>لا يوجد مخزون فعلي</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="stock-cards d-md-none">
                    @forelse($actualStock as $item)
                        <div class="stock-card">
                            <div class="stock-card-header">
                                <div class="stock-card-title">
                                    <i class="bi bi-box-seam"></i>
                                    {{ $item->name }}
                                </div>
                                <span class="qty-badge qty-actual">{{ number_format($item->quantity) }}</span>
                            </div>
                            <div class="stock-card-body">
                                <div class="stock-info">
                                    <span class="label">السعر:</span>
                                    <span class="value">{{ number_format($item->current_price, 2) }} دينار</span>
                                </div>
                                <div class="stock-info">
                                    <span class="label">القيمة الإجمالية:</span>
                                    <span class="value"><strong>{{ number_format($item->total_value, 2) }} دينار</strong></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>لا يوجد مخزون فعلي</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="reserved">
        <div class="card">
            <div class="card-body">
                <!-- Desktop Table -->
                <div class="modern-table d-none d-md-block">
                    <table>
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>القيمة الإجمالية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservedStock as $item)
                            <tr>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td><span class="qty-badge qty-reserved">{{ number_format($item->quantity) }}</span></td>
                                <td>{{ number_format($item->current_price, 2) }} دينار</td>
                                <td><strong>{{ number_format($item->total_value, 2) }} دينار</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-cell">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>لا يوجد مخزون محجوز</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="stock-cards d-md-none">
                    @forelse($reservedStock as $item)
                        <div class="stock-card">
                            <div class="stock-card-header">
                                <div class="stock-card-title">
                                    <i class="bi bi-hourglass-split"></i>
                                    {{ $item->name }}
                                </div>
                                <span class="qty-badge qty-reserved">{{ number_format($item->quantity) }}</span>
                            </div>
                            <div class="stock-card-body">
                                <div class="stock-info">
                                    <span class="label">السعر:</span>
                                    <span class="value">{{ number_format($item->current_price, 2) }} دينار</span>
                                </div>
                                <div class="stock-info">
                                    <span class="label">القيمة الإجمالية:</span>
                                    <span class="value"><strong>{{ number_format($item->total_value, 2) }} دينار</strong></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>لا يوجد مخزون محجوز</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="pending">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-primary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h6 class="mb-0">المنتجات المحجوزة في الفواتير</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($pendingProducts as $product)
                        <div class="col-md-3 mb-3">
                            <div class="stock-item">
                                <div class="stock-info">
                                    <div class="stock-name">{{ $product->name }}</div>
                                </div>
                                <div class="stock-quantity">{{ number_format($product->total_quantity) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">لا توجد منتجات محجوزة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="modern-table">
                    <table>
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>المتجر</th>
                                <th>التاريخ</th>
                                <th>الإجمالي</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingInvoices as $invoice)
                            <tr>
                                <td><strong>#{{ $invoice->invoice_number }}</strong></td>
                                <td>{{ $invoice->store->name }}</td>
                                <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                <td><strong>{{ number_format($invoice->total_amount, 2) }} د.ع</strong></td>
                                <td>
                                    <a href="{{ route('marketer.sales.show', $invoice->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-cell">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>لا توجد فواتير بانتظار التوثيق</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.stats-content h3 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: var(--text-heading);
}

.stats-content p {
    margin: 4px 0 0 0;
    font-size: 14px;
    color: var(--text-muted);
}

.nav-tabs {
    border-bottom: 1px solid var(--border);
}

.nav-tabs .nav-link {
    color: var(--text-main);
    border: none;
    border-bottom: 3px solid transparent;
    background: none;
    padding: 12px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: var(--primary-light);
    color: var(--primary);
}

.nav-tabs .nav-link.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-content {
    margin-top: 0;
}

.tab-pane .card {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}

.modern-table {
    background: var(--bg-card);
    border-radius: 12px;
    overflow: hidden;
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

.modern-table tbody tr:last-child {
    border-bottom: none;
}

.modern-table td {
    padding: 16px 20px;
    text-align: right;
    font-size: 14px;
    color: var(--text-main);
    vertical-align: middle;
}

.qty-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.qty-actual {
    background: #d1e7dd;
    color: #0f5132;
}

.qty-reserved {
    background: #fff3cd;
    color: #856404;
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

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Mobile Cards */
.stock-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.stock-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stock-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-secondary);
}

.stock-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--text-heading);
    font-size: 14px;
}

.stock-card-body {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.stock-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
}

.stock-info .label {
    color: var(--text-muted);
}

.stock-info .value {
    color: var(--text-main);
}

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

.stock-quantity {
    background: var(--primary);
    color: white;
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-weight: 700;
    font-size: 0.9em;
}
</style>
@endsection
