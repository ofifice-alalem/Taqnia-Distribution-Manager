@extends('layouts.app')

@section('title', 'سحب الأرباح')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-wallet2"></i> سحب الأرباح
    </h2>
    
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newWithdrawalModal">
        <i class="bi bi-plus-circle"></i> طلب سحب جديد
    </button>
</div>

<!-- Balance Card -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="balance-card total">
            <div class="balance-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="balance-info">
                <div class="balance-label">إجمالي الأرباح</div>
                <div class="balance-amount">{{ number_format($totalEarned, 2) }} ريال</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="balance-card withdrawn">
            <div class="balance-icon">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <div class="balance-info">
                <div class="balance-label">المسحوب</div>
                <div class="balance-amount">{{ number_format($totalWithdrawn, 2) }} ريال</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="balance-card available">
            <div class="balance-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="balance-info">
                <div class="balance-label">الرصيد المتاح</div>
                <div class="balance-amount">{{ number_format($availableBalance, 2) }} ريال</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="withdrawalsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab">
            <i class="bi bi-list-ul"></i> طلبات السحب ({{ $requests->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="withdrawals-tab" data-bs-toggle="tab" data-bs-target="#withdrawals" type="button" role="tab">
            <i class="bi bi-check-circle"></i> السحوبات الموثقة ({{ $withdrawals->count() }})
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="withdrawalsTabsContent">
    <!-- Requests Tab -->
    <div class="tab-pane fade show active" id="requests" role="tabpanel">
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المبلغ المطلوب</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                        <th style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td class="fw-bold">{{ number_format($request->requested_amount, 2) }} ريال</td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning">في الانتظار</span>
                            @elseif($request->status === 'approved')
                                <span class="badge bg-success">موافق عليه</span>
                            @elseif($request->status === 'rejected')
                                <span class="badge bg-danger">مرفوض</span>
                            @else
                                <span class="badge bg-secondary">ملغى</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('marketer.withdrawals.print', $request->id) }}" class="btn btn-sm btn-primary flex-fill" target="_blank" title="طباعة">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if($request->status === 'pending')
                                <form action="{{ route('marketer.withdrawals.cancel', $request->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('هل أنت متأكد من إلغاء الطلب؟')" title="إلغاء">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            لا توجد طلبات سحب
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="withdrawal-cards d-md-none">
            @forelse($requests as $request)
                <div class="withdrawal-card">
                    <div class="withdrawal-card-header">
                        <div class="withdrawal-number">#{{ $request->id }}</div>
                        <div>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning">في الانتظار</span>
                            @elseif($request->status === 'approved')
                                <span class="badge bg-success">موافق عليه</span>
                            @elseif($request->status === 'rejected')
                                <span class="badge bg-danger">مرفوض</span>
                            @else
                                <span class="badge bg-secondary">ملغى</span>
                            @endif
                        </div>
                    </div>
                    <div class="withdrawal-card-body">
                        <div class="withdrawal-info">
                            <span class="label">المبلغ المطلوب:</span>
                            <span class="value fw-bold">{{ number_format($request->requested_amount, 2) }} ريال</span>
                        </div>
                        <div class="withdrawal-info">
                            <span class="label">تاريخ الطلب:</span>
                            <span class="value">{{ $request->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                    <div class="withdrawal-card-footer">
                        <a href="{{ route('marketer.withdrawals.print', $request->id) }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="bi bi-printer"></i> طباعة
                        </a>
                        @if($request->status === 'pending')
                        <form action="{{ route('marketer.withdrawals.cancel', $request->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من إلغاء الطلب؟')">
                                <i class="bi bi-x-circle"></i> إلغاء
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    لا توجد طلبات سحب
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Withdrawals Tab -->
    <div class="tab-pane fade" id="withdrawals" role="tabpanel">
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المبلغ</th>
                        <th>تاريخ التوثيق</th>
                        <th>المسؤول</th>
                        <th style="width: 120px;">الإيصال</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                    <tr>
                        <td>#{{ $withdrawal->request->id }}</td>
                        <td class="fw-bold text-success">{{ number_format($withdrawal->amount, 2) }} ريال</td>
                        <td>{{ $withdrawal->confirmed_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $withdrawal->admin->full_name }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $withdrawal->signed_receipt_image) }}" target="_blank" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-file-earmark-image"></i> عرض
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            لا توجد سحوبات موثقة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="withdrawal-cards d-md-none">
            @forelse($withdrawals as $withdrawal)
                <div class="withdrawal-card approved">
                    <div class="withdrawal-card-header">
                        <div class="withdrawal-number">#{{ $withdrawal->request->id }}</div>
                        <span class="badge bg-success">موثق</span>
                    </div>
                    <div class="withdrawal-card-body">
                        <div class="withdrawal-info">
                            <span class="label">المبلغ:</span>
                            <span class="value fw-bold text-success">{{ number_format($withdrawal->amount, 2) }} ريال</span>
                        </div>
                        <div class="withdrawal-info">
                            <span class="label">تاريخ التوثيق:</span>
                            <span class="value">{{ $withdrawal->confirmed_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="withdrawal-info">
                            <span class="label">المسؤول:</span>
                            <span class="value">{{ $withdrawal->admin->full_name }}</span>
                        </div>
                    </div>
                    <div class="withdrawal-card-footer">
                        <a href="{{ asset('storage/' . $withdrawal->signed_receipt_image) }}" target="_blank" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-file-earmark-image"></i> عرض الإيصال
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    لا توجد سحوبات موثقة
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- New Withdrawal Modal -->
<div class="modal fade" id="newWithdrawalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">طلب سحب جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('marketer.withdrawals.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        الرصيد المتاح للسحب: <strong>{{ number_format($availableBalance, 2) }} ريال</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">المبلغ المطلوب <span class="text-danger">*</span></label>
                        <input type="number" name="requested_amount" class="form-control" step="0.01" min="1" max="{{ $availableBalance }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                </div>
            </form>
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

.balance-card {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-right: 4px solid;
    border: 1px solid var(--border);
}

.balance-card.total {
    border-color: #0d6efd;
}

.balance-card.withdrawn {
    border-color: #dc3545;
}

.balance-card.available {
    border-color: #198754;
}

.balance-icon {
    font-size: 2.5em;
    opacity: 0.8;
}

.balance-card.total .balance-icon {
    color: #0d6efd;
}

.balance-card.withdrawn .balance-icon {
    color: #dc3545;
}

.balance-card.available .balance-icon {
    color: #198754;
}

.balance-label {
    font-size: 0.9em;
    color: var(--text-secondary);
    margin-bottom: 5px;
}

.balance-amount {
    font-size: 1.5em;
    font-weight: 700;
    color: var(--text-heading);
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

.table {
    background-color: var(--bg-card) !important;
}

.table thead th {
    background-color: var(--bg-main) !important;
    color: var(--text-muted) !important;
    font-weight: 600;
    border-color: var(--border) !important;
    padding: 15px;
}

.table tbody tr {
    transition: background-color 0.2s;
}

.table tbody tr:hover {
    background-color: var(--primary-light) !important;
}

.table tbody td {
    background-color: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
    padding: 15px;
    vertical-align: middle;
}

/* Withdrawal Cards */
.withdrawal-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.withdrawal-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.withdrawal-card.approved {
    border-right: 4px solid #10b981;
}

.withdrawal-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-secondary);
}

.withdrawal-number {
    font-weight: 700;
    color: var(--text-heading);
    font-size: 15px;
}

.withdrawal-card-body {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.withdrawal-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
}

.withdrawal-info .label {
    color: var(--text-muted);
}

.withdrawal-info .value {
    color: var(--text-main);
}

.withdrawal-card-footer {
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: var(--bg-secondary);
    display: flex;
    gap: 8px;
}

@media (max-width: 768px) {
    .withdrawal-cards {
        padding-bottom: 80px;
    }
}
</style>
@endsection
