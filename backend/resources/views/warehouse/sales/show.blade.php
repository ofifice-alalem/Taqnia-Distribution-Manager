@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة')

@section('content')
<div class="row">
    <div class="col-md-4 order-md-2 order-first">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear"></i> الإجراءات</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('warehouse.sales.index') }}" class="btn btn-secondary w-100 mb-2">
                    <i class="bi bi-arrow-right"></i> رجوع للقائمة
                </a>
                
                @if($invoice->status == 'pending')
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#confirmModal">
                        <i class="bi bi-check-circle"></i> توثيق البيع
                    </button>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8 order-md-1 order-last">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-receipt"></i> فاتورة بيع - {{ $invoice->invoice_number }}
                    <span class="badge fs-6 @if($invoice->status == 'pending') bg-warning @else bg-success @endif">
                        @if($invoice->status == 'pending') في انتظار التوثيق @else موثق @endif
                    </span>
                </h4>
            </div>
            
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-person-badge"></i> المسوق:</strong> {{ $invoice->marketer->full_name }}</p>
                        <p><strong><i class="bi bi-shop"></i> المتجر:</strong> {{ $invoice->store->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-calendar"></i> التاريخ:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
                        @if($invoice->status == 'approved')
                            <p><strong><i class="bi bi-check-circle"></i> تم التوثيق:</strong> {{ $invoice->confirmed_at }}</p>
                        @endif
                    </div>
                </div>

                <h5><i class="bi bi-boxes"></i> المنتجات</h5>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }} د.ع</td>
                                    <td>{{ number_format($item->total_price, 2) }} د.ع</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="3">الإجمالي</th>
                                <th>{{ number_format($invoice->total_amount, 2) }} د.ع</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($invoice->status == 'pending')
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('warehouse.sales.confirm', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="keeper_id" value="{{ Auth::id() }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">توثيق البيع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">صورة الفاتورة المختومة</label>
                        <input type="file" name="stamped_invoice_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">توثيق</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
