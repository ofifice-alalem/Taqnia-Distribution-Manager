<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\MarketerRequest;
use App\Models\MarketerRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RequestController extends Controller
{
    public function index()
    {
        $marketerId = Auth::id();
        
        $documentedRequests = MarketerRequest::with(['items.product', 'statusDetail.keeper'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'documented')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $waitingDocRequests = MarketerRequest::with(['items.product', 'statusDetail.keeper'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pendingRequests = MarketerRequest::with(['items.product', 'statusDetail.keeper'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $rejectedRequests = MarketerRequest::with(['items.product', 'statusDetail.keeper'])
            ->where('marketer_id', $marketerId)
            ->whereIn('status', ['rejected', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $documentedCount = $documentedRequests->count();
        $waitingDocCount = $waitingDocRequests->count();
        $pendingCount = $pendingRequests->count();
        $rejectedCount = $rejectedRequests->count();

        return view('marketer.requests.index', compact(
            'documentedRequests', 'waitingDocRequests', 'pendingRequests', 'rejectedRequests',
            'documentedCount', 'waitingDocCount', 'pendingCount', 'rejectedCount'
        ));
    }

    public function create()
    {
        $products = Product::with('mainStock')
            ->where('is_active', true)
            ->get();

        return view('marketer.requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        DB::transaction(function() use ($request) {
            $marketerRequest = MarketerRequest::create([
                'invoice_number' => 'REQ-' . time() . '-' . Auth::id(),
                'marketer_id' => Auth::id(),
                'status' => 'pending'
            ]);

            foreach ($request->products as $product) {
                MarketerRequestItem::create([
                    'request_id' => $marketerRequest->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity']
                ]);
            }
        });

        return redirect()->route('marketer.requests.index')->with('success', 'تم إرسال الطلب بنجاح');
    }

    public function show($id)
    {
        $request = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->where('marketer_id', Auth::id())
            ->findOrFail($id);
            
        $documentInfo = DB::table('delivery_confirmation')
            ->where('request_id', $id)
            ->first();

        return view('marketer.requests.show', compact('request', 'documentInfo'));
    }

    public function cancel($id)
    {
        $request = MarketerRequest::with('items')->where('marketer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if (!in_array($request->status, ['pending', 'approved'])) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء طلب تم اتخاذ قرار بشأنه');
        }

        $wasApproved = $request->status == 'approved';

        DB::transaction(function() use ($request, $wasApproved) {
            $request->update(['status' => 'cancelled']);
            
            if ($wasApproved) {
                $request->statusDetail()->delete();
                
                foreach ($request->items as $item) {
                    DB::table('marketer_reserved_stock')
                        ->where('marketer_id', $request->marketer_id)
                        ->where('product_id', $item->product_id)
                        ->decrement('reserved_quantity', $item->quantity);

                    DB::table('main_stock')
                        ->where('product_id', $item->product_id)
                        ->increment('quantity', $item->quantity);
                }
            }
        });
        
        return redirect()->route('marketer.requests.index')->with('success', 'تم إلغاء الطلب بنجاح');
    }

    public function printInvoice($id)
    {
        $request = MarketerRequest::with(['marketer', 'items.product', 'statusDetail.keeper'])
            ->where('marketer_id', Auth::id())
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

        $pdf = Pdf::loadView('marketer.requests.invoice-pdf', $data)->setPaper('a4');

        return $pdf->download('invoice-' . $request->invoice_number . '.pdf');
    }
}
