<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\MarketerRequest;
use App\Models\MarketerRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $marketerId = Auth::id();
        
        // موثق - له delivery_confirmation
        $documentedRequests = MarketerRequest::with(['items.product', 'status.keeper'])
            ->where('marketer_id', $marketerId)
            ->whereHas('status', function($q) {
                $q->where('status', 'approved');
            })
            ->whereExists(function($q) {
                $q->select(DB::raw(1))
                  ->from('delivery_confirmation')
                  ->whereRaw('delivery_confirmation.request_id = marketer_requests.id');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // في انتظار التوثيق - موافق عليه ولكن لا يوجد delivery_confirmation
        $waitingDocRequests = MarketerRequest::with(['items.product', 'status.keeper'])
            ->where('marketer_id', $marketerId)
            ->whereHas('status', function($q) {
                $q->where('status', 'approved');
            })
            ->whereNotExists(function($q) {
                $q->select(DB::raw(1))
                  ->from('delivery_confirmation')
                  ->whereRaw('delivery_confirmation.request_id = marketer_requests.id');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // في انتظار الموافقة - pending أو لا يوجد status
        $pendingRequests = MarketerRequest::with(['items.product', 'status.keeper'])
            ->where('marketer_id', $marketerId)
            ->where(function($q) {
                $q->whereDoesntHave('status')
                  ->orWhereHas('status', function($subQ) {
                      $subQ->where('status', 'pending');
                  });
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        // مرفوضة - rejected أو cancelled
        $rejectedRequests = MarketerRequest::with(['items.product', 'status.keeper'])
            ->where('marketer_id', $marketerId)
            ->whereHas('status', function($q) {
                $q->whereIn('status', ['rejected', 'cancelled']);
            })
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
                'marketer_id' => Auth::id()
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
        $request = MarketerRequest::with(['marketer', 'items.product', 'status.keeper'])
            ->where('marketer_id', Auth::id())
            ->findOrFail($id);
            
        $documentInfo = DB::table('delivery_confirmation')
            ->where('request_id', $id)
            ->first();

        return view('marketer.requests.show', compact('request', 'documentInfo'));
    }

    public function cancel($id)
    {
        $request = MarketerRequest::where('marketer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($request->status && $request->status->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء طلب تم اتخاذ قرار بشأنه');
        }

        $request->delete();
        
        return redirect()->route('marketer.requests.index')->with('success', 'تم إلغاء الطلب بنجاح');
    }
}