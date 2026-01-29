@extends('layouts.app')

@section('title', 'إدارة العروض')

@section('content')
<!-- المخزون الرئيسي -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center gap-2">
            <div class="icon-box bg-primary">
                <i class="bi bi-box-seam"></i>
            </div>
            <h6 class="mb-0">المخزون الرئيسي</h6>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-3 mb-3">
                    <div class="stock-item">
                        <div class="stock-info">
                            <div class="stock-name">{{ $product->name }}</div>
                            <div class="stock-price">{{ number_format($product->current_price, 2) }} د.ع</div>
                        </div>
                        <div class="stock-quantity">{{ $product->mainStock->quantity ?? 0 }}</div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted mb-0">لا يوجد مخزون</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-gift"></i> إدارة العروض
    </h2>
    
    <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> إضافة عرض جديد
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5>العروض الحالية</h5>
    </div>
    <div class="card-body">
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الحد الأدنى</th>
                        <th>الهدية</th>
                        <th>من</th>
                        <th>إلى</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promotion)
                        <tr>
                            <td>{{ $promotion->product->name }}</td>
                            <td>{{ $promotion->min_quantity }}</td>
                            <td>{{ $promotion->free_quantity }}</td>
                            <td>{{ $promotion->start_date->format('Y-m-d') }}</td>
                            <td>{{ $promotion->end_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge @if($promotion->is_active) bg-success @else bg-danger @endif">
                                    @if($promotion->is_active) نشط @else متوقف @endif
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-primary" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.promotions.toggle', $promotion->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" title="تفعيل/تعطيل">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد عروض</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="d-md-none">
            @forelse($promotions as $promotion)
                <div class="promo-card mb-3">
                    <div class="promo-card-header">
                        <div class="promo-product">{{ $promotion->product->name }}</div>
                        <span class="badge @if($promotion->is_active) bg-success @else bg-danger @endif">
                            @if($promotion->is_active) نشط @else متوقف @endif
                        </span>
                    </div>
                    <div class="promo-card-body">
                        <div class="promo-info">
                            <span class="promo-label">الحد الأدنى:</span>
                            <span class="promo-value">{{ $promotion->min_quantity }}</span>
                        </div>
                        <div class="promo-info">
                            <span class="promo-label">الهدية:</span>
                            <span class="promo-value">{{ $promotion->free_quantity }}</span>
                        </div>
                        <div class="promo-info">
                            <span class="promo-label">الفترة:</span>
                            <span class="promo-value">{{ $promotion->start_date->format('Y-m-d') }} - {{ $promotion->end_date->format('Y-m-d') }}</span>
                        </div>
                    </div>
                    <div class="promo-card-footer">
                        <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> تعديل
                        </a>
                        <form action="{{ route('admin.promotions.toggle', $promotion->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="bi bi-power"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">لا توجد عروض</p>
            @endforelse
        </div>
    </div>
</div>

<style>
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

.stock-price {
    font-size: 0.85em;
    color: var(--text-muted);
}

.stock-quantity {
    background: var(--primary);
    color: white;
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-weight: 700;
    font-size: 0.9em;
}

.promo-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.promo-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border);
}

.promo-product {
    font-weight: 600;
    color: var(--text-heading);
}

.promo-card-body {
    padding: 12px 16px;
}

.promo-info {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid var(--border);
}

.promo-info:last-child {
    border-bottom: none;
}

.promo-label {
    color: var(--text-muted);
    font-size: 0.9em;
}

.promo-value {
    color: var(--text-heading);
    font-weight: 600;
    font-size: 0.9em;
}

.promo-card-footer {
    display: flex;
    gap: 8px;
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: var(--bg-secondary);
}

@media (max-width: 768px) {
    .stock-item {
        padding: 10px 12px;
    }
    
    .stock-name {
        font-size: 0.9em;
    }
    
    .stock-price {
        font-size: 0.8em;
    }
}
</style>
@endsection
