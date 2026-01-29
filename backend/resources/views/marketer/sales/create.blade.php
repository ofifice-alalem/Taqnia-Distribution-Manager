@extends('layouts.app')

@section('title', 'فاتورة بيع جديدة')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-receipt"></i> فاتورة بيع جديدة</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('marketer.sales.store') }}" method="POST" id="salesForm">
                    @csrf
                    <input type="hidden" name="marketer_id" value="{{ Auth::id() }}">
                    
                    <div class="mb-3">
                        <label class="form-label">المتجر</label>
                        <select name="store_id" class="form-select" required>
                            <option value="">اختر المتجر</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }} - {{ $store->owner_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المنتجات</label>
                        <div id="products-container">
                            <div class="product-row border rounded p-3 mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">المنتج</label>
                                        <select name="items[0][product_id]" class="form-select product-select" required>
                                            <option value="">اختر المنتج</option>
                                            @foreach($marketerStock as $stock)
                                                <option value="{{ $stock->product_id }}" 
                                                    data-stock="{{ $stock->quantity }}" 
                                                    data-price="{{ $stock->product->current_price }}"
                                                    data-promo-min="{{ $promotions[$stock->product_id]->min_quantity ?? 0 }}"
                                                    data-promo-free="{{ $promotions[$stock->product_id]->free_quantity ?? 0 }}"
                                                    data-promo-id="{{ $promotions[$stock->product_id]->id ?? '' }}">
                                                    {{ $stock->product->name }} (متوفر: {{ $stock->quantity }}) - {{ number_format($stock->product->current_price, 2) }} د.ع
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">الكمية</label>
                                        <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" required>
                                    </div>
                                    <div class="col-md-2 free-quantity-col" style="display:none;">
                                        <label class="form-label">هدية</label>
                                        <input type="number" name="items[0][free_quantity]" class="form-control free-quantity-input" value="0" readonly>
                                        <input type="hidden" name="items[0][promotion_id]" class="promotion-id-input">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end remove-btn-col">
                                        <button type="button" class="btn btn-danger remove-product" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="promo-info" style="display:none; margin-top:10px;">
                                    <div class="promo-info-box">
                                        <i class="bi bi-info-circle"></i> <span class="promo-info-text"></span>
                                    </div>
                                </div>
                                <div class="promo-alert" style="display:none; margin-top:10px;">
                                    <div class="promo-alert-box">
                                        <i class="bi bi-gift"></i> <span class="promo-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-product" class="btn btn-outline-primary">
                            <i class="bi bi-plus"></i> إضافة منتج
                        </button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('marketer.sales.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-right"></i> رجوع
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> إنشاء الفاتورة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.promo-alert-box {
    background: linear-gradient(135deg, #d1f4e0 0%, #a8e6cf 100%);
    border: 1px solid #10b981;
    color: #065f46;
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 0;
}

html[data-theme="dark"] .promo-alert-box {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
    border: 1px solid #10b981;
    color: #d1fae5;
}

.promo-info-box {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 1px solid #3b82f6;
    color: #1e40af;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 0;
    font-size: 13px;
}

html[data-theme="dark"] .promo-info-box {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    border: 1px solid #3b82f6;
    color: #dbeafe;
}
</style>
<script>
let productIndex = 1;

document.getElementById('add-product').addEventListener('click', function() {
    const container = document.getElementById('products-container');
    const newRow = document.createElement('div');
    newRow.className = 'product-row border rounded p-3 mb-2';
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">المنتج</label>
                <select name="items[${productIndex}][product_id]" class="form-select product-select" required>
                    <option value="">اختر المنتج</option>
                    @foreach($marketerStock as $stock)
                        <option value="{{ $stock->product_id }}" 
                            data-stock="{{ $stock->quantity }}" 
                            data-price="{{ $stock->product->current_price }}"
                            data-promo-min="{{ $promotions[$stock->product_id]->min_quantity ?? 0 }}"
                            data-promo-free="{{ $promotions[$stock->product_id]->free_quantity ?? 0 }}"
                            data-promo-id="{{ $promotions[$stock->product_id]->id ?? '' }}">
                            {{ $stock->product->name }} (متوفر: {{ $stock->quantity }}) - {{ number_format($stock->product->current_price, 2) }} د.ع
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">الكمية</label>
                <input type="number" name="items[${productIndex}][quantity]" class="form-control quantity-input" min="1" required>
            </div>
            <div class="col-md-2 free-quantity-col" style="display:none;">
                <label class="form-label">هدية</label>
                <input type="number" name="items[${productIndex}][free_quantity]" class="form-control free-quantity-input" value="0" readonly>
                <input type="hidden" name="items[${productIndex}][promotion_id]" class="promotion-id-input">
            </div>
            <div class="col-md-2 d-flex align-items-end remove-btn-col">
                <button type="button" class="btn btn-danger remove-product">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="promo-info" style="display:none; margin-top:10px;">
            <div class="promo-info-box">
                <i class="bi bi-info-circle"></i> <span class="promo-info-text"></span>
            </div>
        </div>
        <div class="promo-alert" style="display:none; margin-top:10px;">
            <div class="promo-alert-box">
                <i class="bi bi-gift"></i> <span class="promo-text"></span>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    productIndex++;
    updateRemoveButtons();
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-product') || e.target.parentElement.classList.contains('remove-product')) {
        const row = e.target.closest('.product-row');
        row.remove();
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.product-row');
    rows.forEach((row) => {
        const removeBtn = row.querySelector('.remove-product');
        removeBtn.disabled = rows.length === 1;
    });
}

// التحقق من العروض عند اختيار المنتج
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        const row = e.target.closest('.product-row');
        const selectedOption = e.target.options[e.target.selectedIndex];
        const freeCol = row.querySelector('.free-quantity-col');
        const promoInfo = row.querySelector('.promo-info');
        const promoInfoText = row.querySelector('.promo-info-text');
        const removeBtnCol = row.querySelector('.remove-btn-col');
        
        if (selectedOption && selectedOption.value) {
            const promoMin = parseInt(selectedOption.dataset.promoMin) || 0;
            const promoFree = parseInt(selectedOption.dataset.promoFree) || 0;
            
            if (promoMin > 0) {
                freeCol.style.display = 'block';
                promoInfo.style.display = 'block';
                promoInfoText.textContent = `إذا اشتريت ${promoMin} قطعة تحصل على ${promoFree} مجاناً`;
                removeBtnCol.classList.remove('col-md-2');
                removeBtnCol.classList.add('col-md-1');
            } else {
                freeCol.style.display = 'none';
                promoInfo.style.display = 'none';
                removeBtnCol.classList.remove('col-md-1');
                removeBtnCol.classList.add('col-md-2');
            }
        }
    }
});

// التحقق من العروض
 document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input')) {
        const row = e.target.closest('.product-row');
        const productSelect = row.querySelector('.product-select');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = parseInt(e.target.value) || 0;
        
        if (selectedOption && selectedOption.value) {
            const availableStock = parseInt(selectedOption.dataset.stock);
            const promoMin = parseInt(selectedOption.dataset.promoMin) || 0;
            const promoFree = parseInt(selectedOption.dataset.promoFree) || 0;
            const promoId = selectedOption.dataset.promoId;
            
            const freeInput = row.querySelector('.free-quantity-input');
            const promoIdInput = row.querySelector('.promotion-id-input');
            const promoAlert = row.querySelector('.promo-alert');
            const promoText = row.querySelector('.promo-text');
            
            // تحقق من العرض
            if (promoMin > 0 && quantity >= promoMin) {
                freeInput.value = promoFree;
                promoIdInput.value = promoId;
                promoText.textContent = `مبروك! لديك ${promoFree} قطعة مجانية`;
                promoAlert.style.display = 'block';
            } else {
                freeInput.value = 0;
                promoIdInput.value = '';
                promoAlert.style.display = 'none';
            }
            
            // تحقق من المخزون
            const totalQty = quantity + parseInt(freeInput.value);
            if (totalQty > availableStock) {
                alert(`الكمية الإجمالية (${totalQty}) أكبر من المتوفر (${availableStock})`);
                e.target.value = availableStock - parseInt(freeInput.value);
            }
        }
    }
});
</script>
@endsection
