@extends('layouts.app')

@section('title', 'إضافة عرض جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-gift"></i> إضافة عرض جديد</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.promotions.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">المنتج</label>
                        <select name="product_id" id="product_select" class="form-select" required>
                            <option value="">اختر المنتج</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-stock="{{ $product->mainStock->quantity ?? 0 }}" data-price="{{ $product->current_price }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="stock_info" class="mt-2" style="display:none;">
                            <span class="badge bg-info">المخزون: <span id="stock_quantity">0</span> قطعة</span>
                            <span class="badge bg-success">السعر: <span id="product_price">0</span> د.ع</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحد الأدنى للكمية</label>
                            <input type="number" name="min_quantity" class="form-control" min="1" required>
                            <small class="text-muted">عند شراء هذه الكمية أو أكثر</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الكمية المجانية</label>
                            <input type="number" name="free_quantity" class="form-control" min="1" required>
                            <small class="text-muted">عدد القطع المجانية</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ البداية</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ النهاية</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-right"></i> رجوع
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('product_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const stock = selectedOption.dataset.stock || 0;
    const price = selectedOption.dataset.price || 0;
    const stockInfo = document.getElementById('stock_info');
    const stockQuantity = document.getElementById('stock_quantity');
    const productPrice = document.getElementById('product_price');
    
    if (this.value) {
        stockQuantity.textContent = stock;
        productPrice.textContent = parseFloat(price).toFixed(2);
        stockInfo.style.display = 'block';
    } else {
        stockInfo.style.display = 'none';
    }
});
</script>
@endsection
