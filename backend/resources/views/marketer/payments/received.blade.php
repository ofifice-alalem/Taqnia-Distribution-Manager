@extends('layouts.app')

@section('title', 'الإيصالات المستلمة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-cash-stack"></i> الإيصالات المستلمة
    </h2>
    
    <a href="{{ route('marketer.payments.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-right"></i> رجوع
    </a>
</div>

<!-- Summary Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="stat-value">{{ $receivedPayments->count() }}</div>
                    <div class="stat-label">إجمالي الإيصالات</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-icon success">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalReceived, 2) }}</div>
                    <div class="stat-label">إجمالي المبالغ المستلمة (ريال)</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-icon info">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-value">{{ $receivedPayments->where('confirmed_at', '>=', now()->startOfMonth())->count() }}</div>
                    <div class="stat-label">إيصالات هذا الشهر</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-body">
        @if($receivedPayments->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> لا توجد إيصالات مستلمة
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الإيصال</th>
                            <th>المتجر</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>أمين المخزن</th>
                            <th>تاريخ التوثيق</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receivedPayments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $payment->payment_number }}</strong></td>
                            <td>{{ $payment->store->name }}</td>
                            <td>
                                <span class="amount-badge">
                                    {{ number_format($payment->amount, 2) }} ريال
                                </span>
                            </td>
                            <td>
                                @if($payment->payment_method === 'cash')
                                    <span class="badge bg-success">كاش</span>
                                @elseif($payment->payment_method === 'transfer')
                                    <span class="badge bg-info">حوالة</span>
                                @else
                                    <span class="badge bg-warning">شيك مصدق</span>
                                @endif
                            </td>
                            <td>{{ $payment->keeper->full_name }}</td>
                            <td>{{ $payment->confirmed_at }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('marketer.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('marketer.payments.print', $payment->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th colspan="3">الإجمالي</th>
                            <th>{{ number_format($totalReceived, 2) }} ريال</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
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

.stat-box {
    padding: 20px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-light);
    color: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 15px;
}

.stat-icon.success {
    background: #d1fae5;
    color: #065f46;
}

.stat-icon.info {
    background: #dbeafe;
    color: #1e40af;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-heading);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 600;
}

.amount-badge {
    background: #d1fae5;
    color: #065f46;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    display: inline-block;
}

.action-buttons {
    display: flex;
    gap: 6px;
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

.table tbody tr:hover {
    background: var(--primary-light);
}
</style>
@endsection
