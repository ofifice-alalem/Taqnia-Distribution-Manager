<?php

namespace App\Http\Controllers\Warehouse\Returns;

use App\Http\Controllers\Controller;
use App\Models\MarketerReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        $mainStock = DB::table('main_stock')
            ->join('products', 'main_stock.product_id', '=', 'products.id')
            ->select('products.name', 'products.current_price', 'main_stock.quantity')
            ->where('main_stock.quantity', '>', 0)
            ->get();
        
        $returns = MarketerReturnRequest::with(['items.product', 'marketer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $documentedReturns = $returns->where('status', 'documented');
        $approvedReturns = $returns->where('status', 'approved');
        $pendingReturns = $returns->where('status', 'pending');
        $rejectedReturns = $returns->whereIn('status', ['rejected', 'cancelled']);
        
        $documentedCount = $documentedReturns->count();
        $approvedCount = $approvedReturns->count();
        $pendingCount = $pendingReturns->count();
        $rejectedCount = $rejectedReturns->count();
        
        return view('warehouse.returns.index', compact(
            'mainStock',
            'documentedReturns', 'approvedReturns', 'pendingReturns', 'rejectedReturns',
            'documentedCount', 'approvedCount', 'pendingCount', 'rejectedCount'
        ));
    }

    public function show($id)
    {
        $return = MarketerReturnRequest::with(['items.product', 'marketer', 'keeper', 'approvedBy', 'documentedBy'])
            ->findOrFail($id);
        
        return view('warehouse.returns.show', compact('return'));
    }

    public function approve($id)
    {
        $return = MarketerReturnRequest::findOrFail($id);
        $return->update([
            'status' => 'approved',
            'keeper_id' => Auth::id(),
            'approved_by' => Auth::id()
        ]);
        
        return redirect()->back()->with('success', 'تم الموافقة على طلب الإرجاع');
    }

    public function reject($id)
    {
        $return = MarketerReturnRequest::findOrFail($id);
        $return->update([
            'status' => 'rejected',
            'keeper_id' => Auth::id(),
            'approved_by' => Auth::id()
        ]);
        
        return redirect()->back()->with('success', 'تم رفض طلب الإرجاع');
    }

    public function uploadDocument($id)
    {
        $return = MarketerReturnRequest::with(['items.product', 'marketer'])->findOrFail($id);
        return view('warehouse.returns.upload-document', compact('return'));
    }

    public function storeDocument(Request $request, $id)
    {
        $request->validate([
            'stamped_image' => 'required|image|max:2048'
        ]);

        $return = MarketerReturnRequest::findOrFail($id);
        
        DB::beginTransaction();
        try {
            if ($request->hasFile('stamped_image')) {
                $path = $request->file('stamped_image')->store('returns', 'public');
                
                $return->update([
                    'stamped_image' => $path,
                    'status' => 'documented',
                    'documented_by' => Auth::id()
                ]);

                // تنفيذ حركة المخزون
                foreach ($return->items as $item) {
                    // خصم من مخزون المسوق
                    DB::table('marketer_actual_stock')
                        ->where('marketer_id', $return->marketer_id)
                        ->where('product_id', $item->product_id)
                        ->decrement('quantity', $item->quantity);
                    
                    // إضافة للمخزن الرئيسي
                    DB::table('main_stock')
                        ->where('product_id', $item->product_id)
                        ->increment('quantity', $item->quantity);
                }
            }

            DB::commit();
            return redirect()->route('warehouse.returns.index')->with('success', 'تم توثيق الإرجاع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التوثيق');
        }
    }

    public function printInvoice($id)
    {
        $return = MarketerReturnRequest::with(['items.product', 'marketer', 'keeper', 'approvedBy', 'documentedBy'])->findOrFail($id);
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $statusMap = [
            'pending' => 'في انتظار الموافقة',
            'approved' => 'موافق عليه',
            'documented' => 'موثق',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغى'
        ];
        
        $data = [
            'invoiceNumber' => $return->invoice_number,
            'date' => \Carbon\Carbon::parse($return->created_at)->format('Y-m-d H:i'),
            'marketerName' => $arabic->utf8Glyphs($return->marketer->full_name ?? $return->marketer->name),
            'keeperName' => $return->keeper ? $arabic->utf8Glyphs($return->keeper->full_name ?? $return->keeper->username) : $arabic->utf8Glyphs('---'),
            'approvedByName' => $return->approvedBy ? $arabic->utf8Glyphs($return->approvedBy->full_name ?? $return->approvedBy->username) : null,
            'documentedByName' => $return->documentedBy ? $arabic->utf8Glyphs($return->documentedBy->full_name ?? $return->documentedBy->username) : null,
            'status' => $arabic->utf8Glyphs($statusMap[$return->status] ?? $return->status),
            'items' => $return->items->map(function($item) use ($arabic) {
                return (object)[
                    'name' => $arabic->utf8Glyphs($item->product->name),
                    'quantity' => $item->quantity
                ];
            }),
            'totalQty' => $return->items->sum('quantity'),
            'labels' => [
                'title' => $arabic->utf8Glyphs('فاتورة إرجاع بضاعة'),
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'approvedBy' => $arabic->utf8Glyphs('وافق عليه'),
                'documentedBy' => $arabic->utf8Glyphs('وثقه'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'status' => $arabic->utf8Glyphs('حالة الطلب'),
                'product' => $arabic->utf8Glyphs('المنتج'),
                'quantity' => $arabic->utf8Glyphs('الكمية'),
                'total' => $arabic->utf8Glyphs('الإجمالي'),
                'marketerSign' => $arabic->utf8Glyphs('توقيع المسوق'),
                'keeperSign' => $arabic->utf8Glyphs('توقيع أمين المخزن'),
            ]
        ];
        
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('marketer.returns.invoice-pdf', $data);
        
        return $pdf->download('return-invoice-' . $return->invoice_number . '.pdf');
    }
}
