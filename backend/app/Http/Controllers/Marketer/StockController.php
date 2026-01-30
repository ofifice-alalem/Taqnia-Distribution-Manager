<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Sales\SalesInvoice;

class StockController extends Controller
{
    public function index()
    {
        $marketerId = Auth::id();
        
        $actualStock = DB::table('marketer_actual_stock')
            ->join('products', 'marketer_actual_stock.product_id', '=', 'products.id')
            ->where('marketer_actual_stock.marketer_id', $marketerId)
            ->where('marketer_actual_stock.quantity', '>', 0)
            ->select(
                'products.id',
                'products.name',
                'products.current_price',
                'marketer_actual_stock.quantity',
                DB::raw('marketer_actual_stock.quantity * products.current_price as total_value')
            )
            ->get();
        
        $reservedStock = DB::table('marketer_reserved_stock')
            ->join('products', 'marketer_reserved_stock.product_id', '=', 'products.id')
            ->where('marketer_reserved_stock.marketer_id', $marketerId)
            ->where('marketer_reserved_stock.reserved_quantity', '>', 0)
            ->select(
                'products.id',
                'products.name',
                'products.current_price',
                'marketer_reserved_stock.reserved_quantity as quantity',
                DB::raw('marketer_reserved_stock.reserved_quantity * products.current_price as total_value')
            )
            ->get();
        
        // جلب الفواتير غير الموثقة
        $pendingInvoices = SalesInvoice::with(['store', 'items.product'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'pending')
            ->get();
        
        // حساب المنتجات المحجوزة في الفواتير
        $pendingProducts = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.invoice_id', '=', 'sales_invoices.id')
            ->join('products', 'sales_invoice_items.product_id', '=', 'products.id')
            ->where('sales_invoices.marketer_id', $marketerId)
            ->where('sales_invoices.status', 'pending')
            ->select(
                'products.name',
                DB::raw('SUM(sales_invoice_items.quantity + sales_invoice_items.free_quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.name')
            ->get();
        
        return view('marketer.stock.index', compact('actualStock', 'reservedStock', 'pendingInvoices', 'pendingProducts'));
    }
}
