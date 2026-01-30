@extends('layouts.app')

@section('title', 'سجل الديون - ' . $store->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-journal-text"></i> سجل الديون - {{ $store->name }}
    </h2>
    
    <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-right"></i> رجوع
    </a>
</div>

<!-- Store Info Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>اسم المتجر:</strong> {{ $store->name }}
            </div>
            <div class="col-md-3">
                <strong>المالك:</strong> {{ $store->owner_name ?? '-' }}
            </div>
            <div class="col-md-3">
                <strong>الهاتف:</strong> {{ $store->phone ?? '-' }}
            </div>
            <div class="col-md-3">
                <strong>الدين الحالي:</strong> 
                <span class="badge bg-danger">{{ number_format($store->total_debt, 2) }} ريال</span>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="ledgerTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
            <i class="bi bi-receipt"></i> الفواتير ({{ $invoices->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
            <i class="bi bi-cash-coin"></i> الأقساط المدفوعة ({{ $payments->count() }})
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="ledgerTabsContent">
    <!-- الفواتير -->
    <div class="tab-pane fade show active" id="invoices" role="tabpanel">
        <div class="card">
            <div class="card-body">
                @if($invoices->isEmpty())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> لا توجد فواتير لهذا المتجر
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>المسوق</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->marketer->full_name }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }} ريال</td>
                                    <td>{{ $invoice->confirmed_at ?? '-' }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <th colspan="2">الإجمالي</th>
                                    <th>{{ number_format($invoices->sum('total_amount'), 2) }} ريال</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- الأقساط المدفوعة -->
    <div class="tab-pane fade" id="payments" role="tabpanel">
        <div class="card">
            <div class="card-body">
                @if($payments->isEmpty())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> لا توجد أقساط مدفوعة لهذا المتجر
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الإيصال</th>
                                    <th>المسوق</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>أمين المخزن</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_number }}</td>
                                    <td>{{ $payment->marketer->full_name }}</td>
                                    <td>{{ number_format($payment->amount, 2) }} ريال</td>
                                    <td>
                                        @if($payment->payment_method === 'cash')
                                            <span class="badge bg-success">كاش</span>
                                        @elseif($payment->payment_method === 'transfer')
                                            <span class="badge bg-info">حوالة</span>
                                        @else
                                            <span class="badge bg-warning">شيك مصدق</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->keeper->full_name ?? '-' }}</td>
                                    <td>{{ $payment->confirmed_at ?? '-' }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <th colspan="2">الإجمالي</th>
                                    <th>{{ number_format($payments->sum('amount'), 2) }} ريال</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
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

.card {
    background: var(--bg-card);
    border: 1px solid var(--border);
}

.card-body {
    color: var(--text-main);
}

.table {
    color: var(--text-main);
}

.table thead th {
    background: var(--bg-main);
    color: var(--text-heading);
    border-color: var(--border);
}

.table td {
    border-color: var(--border);
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
</style>
@endsection
