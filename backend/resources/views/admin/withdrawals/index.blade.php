@extends('layouts.app')

@section('title', 'طلبات سحب الأرباح')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-wallet2"></i> طلبات سحب الأرباح
    </h2>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="withdrawalsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
            <i class="bi bi-clock"></i> في الانتظار ({{ $requests->where('status', 'pending')->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
            <i class="bi bi-check-circle"></i> موافق عليها ({{ $requests->where('status', 'approved')->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
            <i class="bi bi-x-circle"></i> مرفوضة ({{ $requests->where('status', 'rejected')->count() }})
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="withdrawalsTabsContent">
    <!-- Pending Tab -->
    <div class="tab-pane fade show active" id="pending" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المسوق</th>
                        <th>المبلغ المطلوب</th>
                        <th>تاريخ الطلب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests->where('status', 'pending') as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->marketer->full_name }}</td>
                        <td class="fw-bold">{{ number_format($request->requested_amount, 2) }} ريال</td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.withdrawals.show', $request->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            لا توجد طلبات في الانتظار
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Approved Tab -->
    <div class="tab-pane fade" id="approved" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المسوق</th>
                        <th>المبلغ</th>
                        <th>تاريخ الطلب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests->where('status', 'approved') as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->marketer->full_name }}</td>
                        <td class="fw-bold text-success">{{ number_format($request->requested_amount, 2) }} ريال</td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.withdrawals.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> عرض
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            لا توجد طلبات موافق عليها
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Rejected Tab -->
    <div class="tab-pane fade" id="rejected" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المسوق</th>
                        <th>المبلغ</th>
                        <th>تاريخ الطلب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests->where('status', 'rejected') as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->marketer->full_name }}</td>
                        <td class="fw-bold text-danger">{{ number_format($request->requested_amount, 2) }} ريال</td>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.withdrawals.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> عرض
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            لا توجد طلبات مرفوضة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

.table thead th {
    background-color: var(--bg-secondary);
    color: var(--text-heading);
    font-weight: 600;
    border: none;
    padding: 15px;
}

.table tbody tr {
    transition: background-color 0.2s;
}

.table tbody tr:hover {
    background-color: var(--bg-hover);
}

.table tbody td {
    padding: 15px;
    vertical-align: middle;
}
</style>
@endsection
