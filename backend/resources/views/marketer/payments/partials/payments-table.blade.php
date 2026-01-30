@if($payments->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> لا توجد إيصالات في هذه الحالة
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>رقم الإيصال</th>
                    <th>المتجر</th>
                    <th>المبلغ</th>
                    <th>طريقة الدفع</th>
                    <th>التاريخ</th>
                    @if($type === 'approved')
                        <th>أمين المخزن</th>
                        <th>تاريخ التوثيق</th>
                    @endif
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_number }}</td>
                        <td>{{ $payment->store->name }}</td>
                        <td>{{ number_format($payment->amount, 2) }} ريال</td>
                        <td>
                            @if($payment->payment_method === 'cash')
                                <span class="badge bg-success">كاش</span>
                            @elseif($payment->payment_method === 'transfer')
                                <span class="badge bg-info">حوالة</span>
                            @else
                                <span class="badge bg-warning">شيك مصدق</span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at }}</td>
                        @if($type === 'approved')
                            <td>{{ $payment->keeper->full_name ?? '-' }}</td>
                            <td>{{ $payment->confirmed_at ?? '-' }}</td>
                        @endif
                        <td>
                            <a href="{{ route('marketer.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> عرض
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<style>
.table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.table thead {
    background: var(--primary-light);
    color: var(--primary);
}

.table tbody tr:hover {
    background: var(--bg-secondary);
}
</style>
