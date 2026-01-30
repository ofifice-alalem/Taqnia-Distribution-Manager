@extends('layouts.app')

@section('title', 'أرباحي')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-cash-stack"></i> أرباحي
    </h2>
</div>

<!-- Current Rate Card -->
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center justify-content-center">
        <i class="bi bi-percent" style="font-size: 24px; margin-left: 10px;"></i>
        <h5 class="mb-0">نسبة العمولة الحالية: <strong>{{ $currentRate }}%</strong></h5>
    </div>
</div>

<!-- Summary Cards -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="stat-value">{{ $commissions->count() }}</div>
                    <div class="stat-label">إجمالي العمليات</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-icon success">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalCommissions, 2) }}</div>
                    <div class="stat-label">إجمالي العمولات (ريال)</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-icon info">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-value">{{ $commissions->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
                    <div class="stat-label">عمليات هذا الشهر</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Commissions Table -->
<div class="card">
    <div class="card-body">
        @if($commissions->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> لا توجد عمولات مسجلة
            </div>
        @else
            <!-- Desktop Table -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الإيصال</th>
                            <th>المتجر</th>
                            <th>المبلغ</th>
                            <th>النسبة</th>
                            <th>الربح</th>
                            <th>أمين المخزن</th>
                            <th>التاريخ</th>
                            <th style="width: 120px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $commission)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $commission->payment->payment_number }}</strong></td>
                            <td>{{ $commission->store->name }}</td>
                            <td>{{ number_format($commission->payment_amount, 2) }} ريال</td>
                            <td><span class="badge bg-info">{{ $commission->commission_rate }}%</span></td>
                            <td><span class="profit-badge">{{ number_format($commission->commission_amount, 2) }} ريال</span></td>
                            <td>{{ $commission->keeper->full_name }}</td>
                            <td>{{ $commission->created_at instanceof \Carbon\Carbon ? $commission->created_at->format('Y-m-d') : $commission->created_at }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('marketer.payments.show', $commission->payment_id) }}" class="btn btn-sm btn-info flex-fill" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('marketer.payments.print', $commission->payment_id) }}" class="btn btn-sm btn-primary flex-fill" title="طباعة">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th colspan="5">الإجمالي</th>
                            <th>{{ number_format($totalCommissions, 2) }} ريال</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="commission-cards d-md-none">
                @foreach($commissions as $commission)
                    <div class="commission-card">
                        <div class="commission-card-header">
                            <div class="commission-number">#{{ $loop->iteration }} - {{ $commission->payment->payment_number }}</div>
                            <span class="commission-date">{{ $commission->created_at instanceof \Carbon\Carbon ? $commission->created_at->format('Y-m-d') : $commission->created_at }}</span>
                        </div>
                        <div class="commission-card-body">
                            <div class="commission-info">
                                <span class="label">المتجر:</span>
                                <span class="value">{{ $commission->store->name }}</span>
                            </div>
                            <div class="commission-info">
                                <span class="label">المبلغ:</span>
                                <span class="value">{{ number_format($commission->payment_amount, 2) }} ريال</span>
                            </div>
                            <div class="commission-info">
                                <span class="label">النسبة:</span>
                                <span class="value"><span class="badge bg-info">{{ $commission->commission_rate }}%</span></span>
                            </div>
                            <div class="commission-info">
                                <span class="label">الربح:</span>
                                <span class="value"><span class="profit-badge">{{ number_format($commission->commission_amount, 2) }} ريال</span></span>
                            </div>
                            <div class="commission-info">
                                <span class="label">أمين المخزن:</span>
                                <span class="value">{{ $commission->keeper->full_name }}</span>
                            </div>
                        </div>
                        <div class="commission-card-footer">
                            <a href="{{ route('marketer.payments.show', $commission->payment_id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> عرض
                            </a>
                            <a href="{{ route('marketer.payments.print', $commission->payment_id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-printer"></i> طباعة
                            </a>
                        </div>
                    </div>
                @endforeach
                
                <!-- Total Card -->
                <div class="total-card">
                    <div class="total-label">إجمالي العمولات</div>
                    <div class="total-value">{{ number_format($totalCommissions, 2) }} ريال</div>
                </div>
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
    border-radius: 12px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    transition: transform 0.2s;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

.profit-badge {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 700;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
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
    font-weight: 600;
    padding: 15px;
}

.table td {
    border-color: var(--border);
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: var(--primary-light);
    transition: background-color 0.2s;
}

.table tfoot {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: white;
}

.table tfoot th {
    padding: 15px;
    font-weight: 700;
    border: none;
}

/* Commission Cards */
.commission-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.commission-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.commission-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-secondary);
}

.commission-number {
    font-weight: 700;
    color: var(--text-heading);
    font-size: 14px;
}

.commission-date {
    font-size: 12px;
    color: var(--text-muted);
    background: var(--bg-main);
    padding: 4px 8px;
    border-radius: 6px;
}

.commission-card-body {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.commission-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
}

.commission-info .label {
    color: var(--text-muted);
}

.commission-info .value {
    color: var(--text-main);
    font-weight: 500;
}

.commission-card-footer {
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: var(--bg-secondary);
    display: flex;
    gap: 8px;
}

.commission-card-footer .btn {
    flex: 1;
}

.total-card {
    background: linear-gradient(135deg, var(--primary), #1e40af);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.total-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.total-value {
    font-size: 24px;
    font-weight: 700;
}
</style>
@endsection
