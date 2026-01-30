@extends('layouts.app')

@section('title', 'عمولات المسوقين')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-percent"></i> عمولات المسوقين
    </h2>
    
    <a href="{{ route('admin.commissions.settings') }}" class="btn btn-primary">
        <i class="bi bi-gear"></i> إعدادات النسب
    </a>
</div>

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

<div class="card">
    <div class="card-body">
        @if($commissions->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> لا توجد عمولات مسجلة
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الإيصال</th>
                            <th>المسوق</th>
                            <th>المتجر</th>
                            <th>المبلغ</th>
                            <th>النسبة</th>
                            <th>الربح</th>
                            <th>أمين المخزن</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $commission)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $commission->payment->payment_number }}</strong></td>
                            <td>{{ $commission->marketer->full_name }}</td>
                            <td>{{ $commission->store->name }}</td>
                            <td>{{ number_format($commission->payment_amount, 2) }} ريال</td>
                            <td><span class="badge bg-info">{{ $commission->commission_rate }}%</span></td>
                            <td><span class="profit-badge">{{ number_format($commission->commission_amount, 2) }} ريال</span></td>
                            <td>{{ $commission->keeper->full_name }}</td>
                            <td>{{ $commission->created_at }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th colspan="6">الإجمالي</th>
                            <th>{{ number_format($totalCommissions, 2) }} ريال</th>
                            <th colspan="3"></th>
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

.profit-badge {
    background: #d1fae5;
    color: #065f46;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    display: inline-block;
}

.card {
    background: var(--bg-card);
    border: 1px solid var(--border);
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
</style>
@endsection
