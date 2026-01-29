@extends('layouts.app')

@section('title', 'فواتير البيع')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-receipt"></i> فواتير البيع
    </h2>
    
    <a href="{{ route('marketer.sales.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> فاتورة جديدة
    </a>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="salesTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
            <i class="bi bi-check-circle"></i> موثق ({{ $approvedCount }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
            <i class="bi bi-clock"></i> في انتظار التوثيق ({{ $pendingCount }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
            <i class="bi bi-x-circle"></i> ملغى ({{ $cancelledCount }})
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="salesTabsContent">
    <div class="tab-pane fade show active" id="approved" role="tabpanel">
        @include('marketer.sales.partials.invoices-table', ['invoices' => $approvedInvoices, 'type' => 'approved'])
    </div>
    
    <div class="tab-pane fade" id="pending" role="tabpanel">
        @include('marketer.sales.partials.invoices-table', ['invoices' => $pendingInvoices, 'type' => 'pending'])
    </div>
    
    <div class="tab-pane fade" id="cancelled" role="tabpanel">
        @include('marketer.sales.partials.invoices-table', ['invoices' => $cancelledInvoices, 'type' => 'cancelled'])
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
</style>
@endsection
