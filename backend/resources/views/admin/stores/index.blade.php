@extends('layouts.app')

@section('title', 'إدارة المتاجر')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-shop"></i> المتاجر
    </h2>
    
    <a href="#" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> إضافة متجر جديد
    </a>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="storesTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-stores-tab" data-bs-toggle="tab" data-bs-target="#all-stores" type="button" role="tab">
            <i class="bi bi-shop"></i> جميع المتاجر ({{ $stores->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="promotions-tab" data-bs-toggle="tab" data-bs-target="#promotions" type="button" role="tab">
            <i class="bi bi-gift"></i> الهدايا والتخفيضات ({{ $promotionInvoices->count() }})
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="storesTabsContent">
    <!-- جميع المتاجر -->
    <div class="tab-pane fade show active" id="all-stores" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المتجر</th>
                                <th>الهاتف</th>
                                <th>ما تم دفعه</th>
                                <th>الدين</th>
                                <th>المتبقي</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stores as $store)
                            <tr>
                                <td><span class="store-id">#{{ $loop->iteration }}</span></td>
                                <td>
                                    <div class="store-info">
                                        <div class="store-name">{{ $store->name }}</div>
                                        @if($store->owner_name)
                                        <div class="store-owner">{{ $store->owner_name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($store->phone)
                                    <div class="phone-info">
                                        <i class="bi bi-telephone"></i> {{ $store->phone }}
                                    </div>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="amount-badge paid">
                                        {{ number_format($store->total_paid, 2) }} د.ع
                                    </span>
                                </td>
                                <td>
                                    <span class="amount-badge debt">
                                        {{ number_format($store->total_debt - $store->total_returns, 2) }} د.ع
                                    </span>
                                </td>
                                <td>
                                    @if($store->remaining > 0)
                                    <span class="amount-badge remaining">
                                        {{ number_format($store->remaining, 2) }} د.ع
                                    </span>
                                    @elseif($store->remaining < 0)
                                    <span class="amount-badge credit">
                                        {{ number_format(abs($store->remaining), 2) }} د.ع (رصيد)
                                    </span>
                                    @else
                                    <span class="amount-badge zero">
                                        0.00 د.ع
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="#" class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-info" title="سجل الديون">
                                            <i class="bi bi-journal-text"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-shop"></i>
                                        <p>لا توجد متاجر مسجلة</p>
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
    
    <!-- الهدايا والتخفيضات -->
    <div class="tab-pane fade" id="promotions" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>اسم المتجر</th>
                                <th>الهاتف</th>
                                <th>رقم الفاتورة</th>
                                <th>نوع التخفيض</th>
                                <th>السعر قبل التخفيض</th>
                                <th>مقدار التخفيض</th>
                                <th>السعر النهائي</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promotionInvoices as $invoice)
                            <tr>
                                <td>
                                    <div class="store-info">
                                        <div class="store-name">{{ $invoice->store->name }}</div>
                                        @if($invoice->store->owner_name)
                                        <div class="store-owner">{{ $invoice->store->owner_name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($invoice->store->phone)
                                    <div class="phone-info">
                                        <i class="bi bi-telephone"></i> {{ $invoice->store->phone }}
                                    </div>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="invoice-number">#{{ $invoice->invoice_number }}</span>
                                </td>
                                <td>
                                    <span class="promo-type-badge">
                                        <i class="bi bi-gift"></i> منتجات
                                    </span>
                                </td>
                                <td>
                                    <span class="price-before">{{ number_format($invoice->price_before_discount, 2) }} د.ع</span>
                                </td>
                                <td>
                                    <span class="discount-amount">{{ number_format($invoice->discount_amount, 2) }} د.ع</span>
                                </td>
                                <td>
                                    <span class="final-price">{{ number_format($invoice->total_amount, 2) }} د.ع</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @auth
                                            @if(Auth::user()->isSalesman())
                                                <a href="{{ route('marketer.sales.show', $invoice->id) }}" class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @elseif(Auth::user()->isWarehouseKeeper())
                                                <a href="{{ route('warehouse.sales.show', $invoice->id) }}" class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @else
                                                <a href="#" class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-gift"></i>
                                        <p>لا توجد فواتير بهدايا أو تخفيضات</p>
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
.page-title {
    color: var(--text-heading);
    font-weight: 700;
    font-size: 1.5em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.store-id {
    font-weight: 600;
    color: var(--text-muted);
    font-size: 0.9em;
}

.store-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.store-name {
    font-weight: 600;
    color: var(--text-heading);
    font-size: 0.95em;
}

.store-owner {
    font-size: 0.85em;
    color: var(--text-muted);
}

.phone-info {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-main);
    font-size: 0.9em;
}

.amount-badge {
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 0.85em;
    display: inline-block;
    white-space: nowrap;
}

.amount-badge.paid {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.amount-badge.debt {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.amount-badge.remaining {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.amount-badge.credit {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.amount-badge.zero {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
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
    border-radius: var(--radius-sm);
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

.table td {
    vertical-align: middle;
    padding: 16px 12px;
}

.table thead th {
    font-weight: 700;
    font-size: 0.85em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px;
}

.invoice-number {
    font-weight: 600;
    color: var(--primary);
    font-size: 0.9em;
}

.promo-type-badge {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 0.85em;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.price-before {
    color: var(--text-muted);
    text-decoration: line-through;
    font-size: 0.9em;
}

.discount-amount {
    color: #10b981;
    font-weight: 600;
    font-size: 0.9em;
}

.final-price {
    color: var(--text-heading);
    font-weight: 700;
    font-size: 0.95em;
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
    border-color: var(--primary);
}

.nav-tabs {
    border-bottom: 1px solid var(--border);
}

@media (max-width: 768px) {
    .table {
        font-size: 0.85em;
    }
    
    .action-buttons .btn {
        width: 28px;
        height: 28px;
        font-size: 0.85em;
    }
    
    .amount-badge {
        padding: 4px 8px;
        font-size: 0.75em;
    }
}
</style>
@endsection
