<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\MainStock;
use App\Models\MarketerRequest;
use App\Models\MarketerRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $documentedRequests = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->where('status', 'documented')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $waitingDocRequests = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pendingRequests = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $rejectedRequests = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->whereIn('status', ['rejected', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $mainStock = DB::table('main_stock')
            ->join('products', 'main_stock.product_id', '=', 'products.id')
            ->select('products.name', 'main_stock.quantity', 'products.current_price')
            ->orderBy('products.name')
            ->get();
            
        $documentedCount = $documentedRequests->count();
        $waitingDocCount = $waitingDocRequests->count();
        $pendingCount = $pendingRequests->count();
        $rejectedCount = $rejectedRequests->count();

        return view('warehouse.requests.index', compact(
            'documentedRequests', 'waitingDocRequests', 'pendingRequests', 'rejectedRequests',
            'documentedCount', 'waitingDocCount', 'pendingCount', 'rejectedCount', 'mainStock'
        ));
    }

    public function show($id)
    {
        $request = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->findOrFail($id);
            
        $documentInfo = DB::table('delivery_confirmation')
            ->where('request_id', $id)
            ->first();

        return view('warehouse.requests.show', compact('request', 'documentInfo'));
    }

    public function approve($id)
    {
        $marketerRequest = MarketerRequest::with('items.product')->findOrFail($id);

        foreach ($marketerRequest->items as $item) {
            $mainStock = MainStock::where('product_id', $item->product_id)->first();
            if (!$mainStock || $mainStock->quantity < $item->quantity) {
                return redirect()->back()->with('error', 'الكمية المطلوبة غير متوفرة للمنتج: ' . $item->product->name);
            }
        }

        DB::transaction(function() use ($marketerRequest) {
            $marketerRequest->update(['status' => 'approved']);
            
            MarketerRequestStatus::create([
                'request_id' => $marketerRequest->id,
                'marketer_id' => $marketerRequest->marketer_id,
                'keeper_id' => Auth::id(),
                'status' => 'approved'
            ]);

            foreach ($marketerRequest->items as $item) {
                DB::table('main_stock')
                    ->where('product_id', $item->product_id)
                    ->decrement('quantity', $item->quantity);

                DB::table('marketer_reserved_stock')->updateOrInsert(
                    [
                        'marketer_id' => $marketerRequest->marketer_id,
                        'product_id' => $item->product_id
                    ],
                    [
                        'reserved_quantity' => DB::raw('COALESCE(reserved_quantity, 0) + ' . $item->quantity)
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'تم الموافقة على الطلب وتحويل البضاعة للمخزن المحجوز');
    }

    public function reject(Request $request, $id)
    {
        $marketerRequest = MarketerRequest::findOrFail($id);

        $marketerRequest->update(['status' => 'rejected']);
        
        MarketerRequestStatus::create([
            'request_id' => $marketerRequest->id,
            'marketer_id' => $marketerRequest->marketer_id,
            'keeper_id' => Auth::id(),
            'status' => 'rejected',
            'reason' => $request->rejection_reason
        ]);

        return redirect()->back()->with('success', 'تم رفض الطلب');
    }

    public function uploadDocument($id)
    {
        $request = MarketerRequest::with(['marketer', 'items.product', 'statusDetail'])
            ->findOrFail($id);
            
        if ($request->status !== 'approved') {
            return redirect()->back()->with('error', 'يجب الموافقة على الطلب أولاً');
        }

        return view('warehouse.requests.upload-document', compact('request'));
    }

    public function storeDocument(Request $request, $id)
    {
        $request->validate([
            'document_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $marketerRequest = MarketerRequest::with('items')->findOrFail($id);

        $fileName = 'invoice_' . time() . '.' . $request->file('document_image')->getClientOriginalExtension();
        $imagePath = $request->file('document_image')->storeAs(
            'requests/' . $marketerRequest->invoice_number, 
            $fileName, 
            'public'
        );

        DB::transaction(function() use ($marketerRequest, $imagePath) {
            $marketerRequest->update(['status' => 'documented']);
            
            foreach ($marketerRequest->items as $item) {
                DB::table('marketer_reserved_stock')
                    ->where('marketer_id', $marketerRequest->marketer_id)
                    ->where('product_id', $item->product_id)
                    ->decrement('reserved_quantity', $item->quantity);

                DB::table('marketer_actual_stock')->updateOrInsert(
                    [
                        'marketer_id' => $marketerRequest->marketer_id,
                        'product_id' => $item->product_id
                    ],
                    [
                        'quantity' => DB::raw('COALESCE(quantity, 0) + ' . $item->quantity)
                    ]
                );
            }

            DB::table('delivery_confirmation')->insert([
                'request_id' => $marketerRequest->id,
                'keeper_id' => Auth::id(),
                'signed_image' => $imagePath,
                'status' => 'documented',
                'confirmed_at' => now()
            ]);
        });

        return redirect()->route('warehouse.requests.index')->with('success', 'تم توثيق الطلب ونقل البضاعة للمخزن الفعلي');
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $marketerRequest = MarketerRequest::with('items')->findOrFail($id);
        
        if ($marketerRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الطلب');
        }

        DB::transaction(function() use ($marketerRequest, $request) {
            $marketerRequest->update(['status' => 'cancelled']);
            
            $marketerRequest->statusDetail()->update([
                'status' => 'cancelled',
                'reason' => $request->cancellation_reason
            ]);

            foreach ($marketerRequest->items as $item) {
                DB::table('marketer_reserved_stock')
                    ->where('marketer_id', $marketerRequest->marketer_id)
                    ->where('product_id', $item->product_id)
                    ->decrement('reserved_quantity', $item->quantity);

                DB::table('main_stock')
                    ->where('product_id', $item->product_id)
                    ->increment('quantity', $item->quantity);
            }
        });

        return redirect()->route('warehouse.requests.index')->with('success', 'تم إلغاء الطلب وإرجاع الكمية للمخزن الرئيسي');
    }

    public function printInvoice($id)
    {
        $request = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->findOrFail($id);

        $documentInfo = DB::table('delivery_confirmation')
            ->where('request_id', $id)
            ->first();

        $arabic = new \ArPHP\I18N\Arabic();

        $data = [
            'invoiceNumber' => $request->invoice_number,
            'date' => \Carbon\Carbon::parse($request->created_at)->format('Y-m-d H:i'),
            'marketerName' => $arabic->utf8Glyphs($request->marketer->full_name ?? 'غير محدد'),
            'status' => $arabic->utf8Glyphs($request->status_text),
            'keeperName' => $request->statusDetail && $request->statusDetail->keeper ? $arabic->utf8Glyphs($request->statusDetail->keeper->full_name) : '',
            'statusDate' => $request->statusDetail ? \Carbon\Carbon::parse($request->statusDetail->updated_at)->format('Y-m-d H:i') : '',
            'items' => $request->items->map(function($item) use ($arabic) {
                return (object)[
                    'name' => $arabic->utf8Glyphs($item->product->name),
                    'quantity' => $item->quantity,
                    'price' => $item->product->current_price,
                    'total' => $item->quantity * $item->product->current_price
                ];
            }),
            'total' => $request->items->sum(fn($item) => $item->quantity * $item->product->current_price),
            'title' => $arabic->utf8Glyphs('فاتورة طلب بضاعة'),
            'labels' => [
                'invoiceNumber' => $arabic->utf8Glyphs('رقم الفاتورة'),
                'date' => $arabic->utf8Glyphs('تاريخ الطلب'),
                'marketer' => $arabic->utf8Glyphs('اسم المسوق'),
                'status' => $arabic->utf8Glyphs('حالة الطلب'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'statusDate' => $arabic->utf8Glyphs('تاريخ المعالجة'),
                'product' => $arabic->utf8Glyphs('اسم المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'price' => $arabic->utf8Glyphs('السعر'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'grandTotal' => $arabic->utf8Glyphs('الإجمالي الكلي'),
                'currency' => $arabic->utf8Glyphs('دينار'),
                'marketerSign' => $arabic->utf8Glyphs('توقيع المسوق'),
                'keeperSign' => $arabic->utf8Glyphs('توقيع أمين المخزن'),
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('marketer.requests.invoice-pdf', $data)->setPaper('a4');

        return $pdf->download('invoice-' . $request->invoice_number . '.pdf');
    }

    public function rejectApproved(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $marketerRequest = MarketerRequest::with('items')->findOrFail($id);
        
        if ($marketerRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'لا يمكن رفض هذا الطلب');
        }

        DB::transaction(function() use ($marketerRequest, $request) {
            $marketerRequest->update(['status' => 'rejected']);
            
            $marketerRequest->statusDetail()->update([
                'status' => 'rejected',
                'keeper_id' => Auth::id()
            ]);

            foreach ($marketerRequest->items as $item) {
                DB::table('marketer_reserved_stock')
                    ->where('marketer_id', $marketerRequest->marketer_id)
                    ->where('product_id', $item->product_id)
                    ->decrement('reserved_quantity', $item->quantity);

                DB::table('main_stock')
                    ->where('product_id', $item->product_id)
                    ->increment('quantity', $item->quantity);
            }
        });

        return redirect()->route('warehouse.requests.index')->with('success', 'تم رفض الطلب وإرجاع الكمية للمخزن الرئيسي');
    }
}
