@extends('layouts.app')

@section('title', 'إعدادات نسب العمولات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title">
        <i class="bi bi-gear"></i> إعدادات نسب العمولات
    </h2>
    
    <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-right"></i> رجوع
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المسوق</th>
                        <th>نسبة العمولة الحالية</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marketers as $marketer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $marketer->full_name }}</td>
                        <td>
                            <span class="badge bg-info">{{ $marketer->commission_rate ?? 0 }}%</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" onclick="editRate({{ $marketer->id }}, {{ $marketer->commission_rate ?? 0 }})">
                                <i class="bi bi-pencil"></i> تعديل النسبة
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل نسبة العمولة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="commission_rate" class="form-label">نسبة العمولة (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="commission_rate" id="commission_rate" class="form-control" required>
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

<script>
function editRate(marketerId, currentRate) {
    document.getElementById('commission_rate').value = currentRate;
    document.getElementById('editRateForm').action = `/admin/commissions/${marketerId}/update-rate`;
    new bootstrap.Modal(document.getElementById('editRateModal')).show();
}
</script>

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

.modal-content {
    background: var(--bg-card);
    color: var(--text-main);
}

.modal-header {
    background: var(--bg-sidebar);
    border-bottom: 1px solid var(--border);
}

.modal-title {
    color: var(--text-heading);
}
</style>
@endsection
