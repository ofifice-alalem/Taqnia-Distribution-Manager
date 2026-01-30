@extends('layouts.app')

@section('title', 'إدارة العروض')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-gift"></i> إدارة العروض والتخفيضات
    </h2>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="promotionsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="product-promotions-tab" data-bs-toggle="tab" data-bs-target="#product-promotions" type="button" role="tab">
            <i class="bi bi-gift"></i> عروض المنتجات ({{ $promotions->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="invoice-discounts-tab" data-bs-toggle="tab" data-bs-target="#invoice-discounts" type="button" role="tab">
            <i class="bi bi-percent"></i> تخفيضات الفواتير ({{ $discountTiers->count() }})
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="promotionsTabsContent">
    <!-- عروض المنتجات -->
    <div class="tab-pane fade show active" id="product-promotions" role="tabpanel">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> إضافة عرض جديد
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                                            <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.promotions.toggle', $promotion->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-power"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-gift"></i>
                                            <p>لا توجد عروض</p>
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
    
    <!-- تخفيضات الفواتير -->
    <div class="tab-pane fade" id="invoice-discounts" role="tabpanel">
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTierModal">
                <i class="bi bi-plus-circle"></i> إضافة شريحة تخفيض
            </button>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>الحد الأدنى للفاتورة</th>
                                <th>نسبة التخفيض</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discountTiers as $tier)
                                <tr>
                                    <td>
                                        <span class="amount-badge">{{ number_format($tier->min_amount, 2) }} د.ع</span>
                                    </td>
                                    <td>
                                        @if($tier->discount_type === 'percentage')
                                            <span class="discount-badge">{{ $tier->discount_percentage }}%</span>
                                        @else
                                            <span class="discount-badge fixed">{{ number_format($tier->discount_amount, 2) }} د.ع</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge @if($tier->is_active) bg-success @else bg-danger @endif">
                                            @if($tier->is_active) نشط @else متوقف @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editTier({{ $tier->id }}, '{{ $tier->min_amount }}', '{{ $tier->discount_type }}', '{{ $tier->discount_percentage }}', '{{ $tier->discount_amount }}')"
                                                data-bs-toggle="modal" data-bs-target="#editTierModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.discount-tiers.toggle', $tier->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-power"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.discount-tiers.destroy', $tier->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-percent"></i>
                                            <p>لا توجد شرائح تخفيض</p>
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

<!-- Modal إضافة شريحة -->
<div class="modal fade" id="addTierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.discount-tiers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة شريحة تخفيض جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الحد الأدنى للفاتورة (د.ع)</label>
                        <input type="number" name="min_amount" class="form-control" min="0" step="0.01" required>
                        <small class="text-muted">مثال: 1000 د.ع</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع التخفيض</label>
                        <select name="discount_type" id="discountTypeSelect" class="form-select" required>
                            <option value="percentage">نسبة مئوية (%)</option>
                            <option value="fixed">مبلغ ثابت (د.ع)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="percentageField">
                        <label class="form-label">نسبة التخفيض (%)</label>
                        <input type="number" name="discount_percentage" class="form-control" min="0" max="100" step="0.01">
                        <small class="text-muted">مثال: 5%</small>
                    </div>
                    <div class="mb-3" id="amountField" style="display:none;">
                        <label class="form-label">مبلغ التخفيض (د.ع)</label>
                        <input type="number" name="discount_amount" class="form-control" min="0" step="0.01">
                        <small class="text-muted">مثال: 200 د.ع</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تعديل شريحة -->
<div class="modal fade" id="editTierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل شريحة التخفيض</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الحد الأدنى للفاتورة (د.ع)</label>
                        <input type="number" name="min_amount" id="edit_min_amount" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع التخفيض</label>
                        <select name="discount_type" id="edit_discount_type" class="form-select" required>
                            <option value="percentage">نسبة مئوية (%)</option>
                            <option value="fixed">مبلغ ثابت (د.ع)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="edit_percentageField">
                        <label class="form-label">نسبة التخفيض (%)</label>
                        <input type="number" name="discount_percentage" id="edit_discount_percentage" class="form-control" min="0" max="100" step="0.01">
                    </div>
                    <div class="mb-3" id="edit_amountField" style="display:none;">
                        <label class="form-label">مبلغ التخفيض (د.ع)</label>
                        <input type="number" name="discount_amount" id="edit_discount_amount" class="form-control" min="0" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
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

.amount-badge {
    background: var(--primary-light);
    color: var(--primary);
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 0.9em;
}

.discount-badge {
    background: #d1fae5;
    color: #065f46;
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-weight: 700;
    font-size: 0.95em;
}

.discount-badge.fixed {
    background: #dbeafe;
    color: #1e40af;
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
</style>

<script>
document.getElementById('discountTypeSelect').addEventListener('change', function() {
    const percentageField = document.getElementById('percentageField');
    const amountField = document.getElementById('amountField');
    const percentageInput = percentageField.querySelector('input');
    const amountInput = amountField.querySelector('input');
    
    if (this.value === 'percentage') {
        percentageField.style.display = 'block';
        amountField.style.display = 'none';
        percentageInput.required = true;
        amountInput.required = false;
        amountInput.value = '';
    } else {
        percentageField.style.display = 'none';
        amountField.style.display = 'block';
        percentageInput.required = false;
        amountInput.required = true;
        percentageInput.value = '';
    }
});

document.getElementById('edit_discount_type').addEventListener('change', function() {
    const percentageField = document.getElementById('edit_percentageField');
    const amountField = document.getElementById('edit_amountField');
    const percentageInput = document.getElementById('edit_discount_percentage');
    const amountInput = document.getElementById('edit_discount_amount');
    
    if (this.value === 'percentage') {
        percentageField.style.display = 'block';
        amountField.style.display = 'none';
        percentageInput.required = true;
        amountInput.required = false;
        amountInput.value = '';
    } else {
        percentageField.style.display = 'none';
        amountField.style.display = 'block';
        percentageInput.required = false;
        amountInput.required = true;
        percentageInput.value = '';
    }
});

function editTier(id, minAmount, discountType, discountPercentage, discountAmount) {
    document.getElementById('editTierForm').action = `/admin/discount-tiers/${id}`;
    document.getElementById('edit_min_amount').value = minAmount;
    document.getElementById('edit_discount_type').value = discountType;
    
    if (discountType === 'percentage') {
        document.getElementById('edit_percentageField').style.display = 'block';
        document.getElementById('edit_amountField').style.display = 'none';
        document.getElementById('edit_discount_percentage').value = discountPercentage;
        document.getElementById('edit_discount_percentage').required = true;
        document.getElementById('edit_discount_amount').required = false;
    } else {
        document.getElementById('edit_percentageField').style.display = 'none';
        document.getElementById('edit_amountField').style.display = 'block';
        document.getElementById('edit_discount_amount').value = discountAmount;
        document.getElementById('edit_discount_amount').required = true;
        document.getElementById('edit_discount_percentage').required = false;
    }
}
</script>
@endsection
