@extends('layouts.app')

@section('title', 'إيصال قبض جديد')

@section('content')
<div class="mb-4">
    <h2 class="page-title">
        <i class="bi bi-cash-coin"></i> إيصال قبض جديد
    </h2>
</div>

<div class="card">
    <div class="card-body">
        @if($stores->isEmpty())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> لا توجد محلات عليها ديون حالياً
            </div>
            <a href="{{ route('marketer.payments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right"></i> رجوع
            </a>
        @else
        <form action="{{ route('marketer.payments.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="store_id" class="form-label">المتجر <span class="text-danger">*</span></label>
                    <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required onchange="updateDebt()">
                        <option value="">اختر المتجر</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" data-debt="{{ number_format($store->total_debt, 2) }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                {{ $store->name }} - الدين: {{ number_format($store->total_debt, 2) }} ريال
                            </option>
                        @endforeach
                    </select>
                    @error('store_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="debtInfo" class="mt-2" style="display: none;">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> الدين الحالي: <strong id="debtAmount">0.00</strong> ريال
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount" 
                           class="form-control @error('amount') is-invalid @enderror" 
                           value="{{ old('amount') }}" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="payment_method" class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                        <option value="">اختر طريقة الدفع</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>كاش</option>
                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>حوالة</option>
                        <option value="certified_check" {{ old('payment_method') == 'certified_check' ? 'selected' : '' }}>شيك مصدق</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> إنشاء الإيصال
                </button>
                <a href="{{ route('marketer.payments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> إلغاء
                </a>
            </div>
        </form>
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

.card {
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>

<script>
function updateDebt() {
    const select = document.getElementById('store_id');
    const selectedOption = select.options[select.selectedIndex];
    const debtAmount = selectedOption.getAttribute('data-debt');
    const debtInfo = document.getElementById('debtInfo');
    const debtAmountSpan = document.getElementById('debtAmount');
    
    if (debtAmount && select.value) {
        debtAmountSpan.textContent = debtAmount;
        debtInfo.style.display = 'block';
    } else {
        debtInfo.style.display = 'none';
    }
}

// Show debt on page load if store is already selected
document.addEventListener('DOMContentLoaded', function() {
    updateDebt();
});
</script>
@endsection
