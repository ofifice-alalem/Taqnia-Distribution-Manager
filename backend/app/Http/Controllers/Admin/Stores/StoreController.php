<?php

namespace App\Http\Controllers\Admin\Stores;

use App\Http\Controllers\Controller;
use App\Models\Store\Store;
use App\Models\Debt\StoreDebtLedger;
use App\Models\Sales\SalesInvoice;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::select('stores.*')
            ->leftJoin('store_debt_ledger', 'stores.id', '=', 'store_debt_ledger.store_id')
            ->selectRaw('
                stores.*,
                COALESCE(SUM(CASE WHEN store_debt_ledger.entry_type = "sale" THEN store_debt_ledger.amount ELSE 0 END), 0) as total_debt,
                COALESCE(SUM(CASE WHEN store_debt_ledger.entry_type = "return" THEN store_debt_ledger.amount ELSE 0 END), 0) as total_returns,
                COALESCE(SUM(CASE WHEN store_debt_ledger.entry_type = "payment" THEN ABS(store_debt_ledger.amount) ELSE 0 END), 0) as total_paid,
                COALESCE(SUM(store_debt_ledger.amount), 0) as remaining
            ')
            ->groupBy('stores.id', 'stores.name', 'stores.owner_name', 'stores.phone', 'stores.address', 'stores.is_active', 'stores.created_at')
            ->get();

        // جلب الفواتير التي تحتوي على هدايا أو تخفيضات
        $promotionInvoices = SalesInvoice::with(['store', 'items.promotion'])
            ->where(function($query) {
                $query->whereHas('items', function($q) {
                    $q->where('free_quantity', '>', 0);
                })
                ->orWhere('invoice_discount_amount', '>', 0);
            })
            ->where('status', 'approved')
            ->get()
            ->map(function($invoice) {
                // حساب السعر قبل التخفيض
                $invoice->price_before_discount = $invoice->subtotal;
                $invoice->discount_amount = $invoice->product_discount + $invoice->invoice_discount_amount;
                
                return $invoice;
            });

        return view('admin.stores.index', compact('stores', 'promotionInvoices'));
    }

    public function ledger($id)
    {
        $store = Store::findOrFail($id);

        $invoices = SalesInvoice::with(['marketer', 'items.product'])
            ->where('store_id', $id)
            ->where('status', 'approved')
            ->orderBy('confirmed_at', 'desc')
            ->get();

        $payments = \App\Models\Payment\StorePayment::with(['marketer', 'keeper'])
            ->where('store_id', $id)
            ->where('status', 'approved')
            ->orderBy('confirmed_at', 'desc')
            ->get();

        return view('admin.stores.ledger', compact('store', 'invoices', 'payments'));
    }
}
