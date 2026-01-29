<?php

namespace App\Http\Controllers\Marketer\Sales;

use App\Http\Controllers\Controller;
use App\Services\SalesInvoiceService;
use App\Models\Sales\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesInvoiceController extends Controller
{
    protected $salesService;

    public function __construct(SalesInvoiceService $salesService)
    {
        $this->salesService = $salesService;
    }

    public function index()
    {
        $marketerId = Auth::id();

        $approvedInvoices = SalesInvoice::with(['store', 'items.product'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingInvoices = SalesInvoice::with(['store', 'items.product'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelledInvoices = SalesInvoice::with(['store', 'items.product'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedCount = $approvedInvoices->count();
        $pendingCount = $pendingInvoices->count();
        $cancelledCount = $cancelledInvoices->count();

        return view('marketer.sales.index', compact(
            'approvedInvoices', 'pendingInvoices', 'cancelledInvoices',
            'approvedCount', 'pendingCount', 'cancelledCount'
        ));
    }

    public function create()
    {
        $stores = \App\Models\Store\Store::where('is_active', true)->get();
        $products = \App\Models\Product::where('is_active', true)->get();
        $marketerStock = \App\Models\Stock\MarketerActualStock::where('marketer_id', Auth::id())
            ->with('product')
            ->get();
        
        // جلب العروض النشطة
        $promotions = \App\Models\Promotion\ProductPromotion::active()
            ->with('product')
            ->get()
            ->keyBy('product_id');

        return view('marketer.sales.create', compact('stores', 'products', 'marketerStock', 'promotions'));
    }

    public function show($id)
    {
        $invoice = SalesInvoice::with(['store', 'items.product', 'marketer', 'keeper'])
            ->where('marketer_id', Auth::id())
            ->findOrFail($id);

        return view('marketer.sales.show', compact('invoice'));
    }

    public function print($id)
    {
        $invoice = SalesInvoice::with(['marketer', 'store', 'keeper', 'items.product'])
            ->where('marketer_id', Auth::id())
            ->findOrFail($id);

        $arabic = new \ArPHP\I18N\Arabic();

        $data = [
            'invoiceNumber' => $invoice->invoice_number,
            'date' => $invoice->created_at->format('Y-m-d H:i'),
            'marketerName' => $arabic->utf8Glyphs($invoice->marketer->full_name),
            'storeName' => $arabic->utf8Glyphs($invoice->store->name),
            'storeOwner' => $arabic->utf8Glyphs($invoice->store->owner_name),
            'cancelled' => $invoice->status == 'cancelled',
            'cancelledText' => $arabic->utf8Glyphs('الفاتورة ملغية ولا يعتد بها'),
            'items' => $invoice->items->map(function($item) use ($arabic) {
                return (object)[
                    'name' => $arabic->utf8Glyphs($item->product->name),
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'total' => $item->total_price
                ];
            }),
            'total' => $invoice->total_amount,
            'title' => $arabic->utf8Glyphs('فاتورة بيع'),
            'labels' => [
                'invoiceNumber' => $arabic->utf8Glyphs('رقم الفاتورة'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'store' => $arabic->utf8Glyphs('المتجر'),
                'owner' => $arabic->utf8Glyphs('المالك'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'price' => $arabic->utf8Glyphs('السعر'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'grandTotal' => $arabic->utf8Glyphs('الإجمالي الكلي'),
                'currency' => $arabic->utf8Glyphs('دينار'),
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('marketer.sales.invoice-pdf', $data)->setPaper('a4');
        return $pdf->download('sales-' . $invoice->invoice_number . '.pdf');
    }

    public function cancel($id)
    {
        $invoice = SalesInvoice::with('items')
            ->where('marketer_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        \DB::transaction(function () use ($invoice) {
            // إرجاع الكمية لمخزون المسوق
            foreach ($invoice->items as $item) {
                \App\Models\Stock\MarketerActualStock::where('marketer_id', $invoice->marketer_id)
                    ->where('product_id', $item->product_id)
                    ->increment('quantity', $item->quantity);
            }

            // حذف من المخزون المرحلي
            \App\Models\Stock\StorePendingStock::where('sales_invoice_id', $invoice->id)->delete();

            // تحديث حالة الفاتورة
            $invoice->update(['status' => 'cancelled']);
        });

        return redirect()->route('marketer.sales.index')->with('success', 'تم إلغاء الفاتورة بنجاح');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'marketer_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $invoice = $this->salesService->createPendingSale($validated);
            return redirect()->route('marketer.sales.index')->with('success', 'تم إنشاء فاتورة البيع بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
