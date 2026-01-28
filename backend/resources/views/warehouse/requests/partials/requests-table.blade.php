<div class="table-wrapper">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>المسوق</th>
                    <th>التاريخ</th>
                    <th>عدد المنتجات</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>
                            <div class="request-id">
                                <i class="bi bi-file-earmark-text"></i>
                                #{{ $request->id }}
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <i class="bi bi-person"></i>
                                {{ $request->marketer->full_name ?? 'غير محدد' }}
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <i class="bi bi-calendar3"></i>
                                {{ $request->created_at }}
                            </div>
                        </td>
                        <td>
                            <div class="items-count">
                                <i class="bi bi-boxes"></i>
                                {{ $request->items->count() }} منتج
                            </div>
                        </td>
                        <td>
                            <span class="status-badge 
                                @if($type == 'pending') status-pending
                                @elseif($type == 'waiting-doc') status-approved
                                @elseif($type == 'documented') status-documented
                                @else status-rejected
                                @endif">
                                @if($type == 'pending') في انتظار الموافقة
                                @elseif($type == 'waiting-doc') في انتظار التوثيق
                                @elseif($type == 'documented') موثق
                                @else مرفوض
                                @endif
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('warehouse.requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($type == 'pending')
                                    <button class="btn btn-sm btn-success" onclick="approveRequest({{ $request->id }})" title="موافقة">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectRequest({{ $request->id }})" title="رفض">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @elseif($type == 'waiting-doc')
                                    <a href="{{ route('warehouse.requests.upload-document', $request->id) }}" class="btn btn-sm btn-warning" title="توثيق">
                                        <i class="bi bi-camera"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>لا توجد طلبات في هذه الفئة</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>