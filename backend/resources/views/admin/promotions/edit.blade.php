@extends('layouts.app')

@section('title', 'تعديل العرض')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-pencil"></i> تعديل العرض</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">المنتج</label>
                        <select name="product_id" class="form-select" required>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @if($product->id == $promotion->product_id) selected @endif>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحد الأدنى للكمية</label>
                            <input type="number" name="min_quantity" class="form-control" value="{{ $promotion->min_quantity }}" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الكمية المجانية</label>
                            <input type="number" name="free_quantity" class="form-control" value="{{ $promotion->free_quantity }}" min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ البداية</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $promotion->start_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ النهاية</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $promotion->end_date->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-right"></i> رجوع
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> تحديث
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
