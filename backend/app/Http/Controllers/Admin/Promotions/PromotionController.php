<?php

namespace App\Http\Controllers\Admin\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Promotion\ProductPromotion;
use App\Models\Promotion\InvoiceDiscountTier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = ProductPromotion::with(['product', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $discountTiers = InvoiceDiscountTier::orderBy('min_amount', 'asc')->get();
        
        $products = Product::with('mainStock')
            ->where('is_active', true)
            ->get();

        return view('admin.promotions.index', compact('promotions', 'products', 'discountTiers'));
    }

    public function create()
    {
        $products = Product::with('mainStock')->where('is_active', true)->get();
        return view('admin.promotions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'free_quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        ProductPromotion::create([
            ...$validated,
            'created_by' => Auth::id(),
            'is_active' => true
        ]);

        return redirect()->route('admin.promotions.index')->with('success', 'تم إضافة العرض بنجاح');
    }

    public function edit($id)
    {
        $promotion = ProductPromotion::findOrFail($id);
        $products = Product::where('is_active', true)->get();
        return view('admin.promotions.edit', compact('promotion', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'free_quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $promotion = ProductPromotion::findOrFail($id);
        $promotion->update($validated);

        return redirect()->route('admin.promotions.index')->with('success', 'تم تحديث العرض بنجاح');
    }

    public function toggleStatus($id)
    {
        $promotion = ProductPromotion::findOrFail($id);
        $promotion->update(['is_active' => !$promotion->is_active]);

        return redirect()->back()->with('success', 'تم تحديث حالة العرض');
    }

    public function destroy($id)
    {
        ProductPromotion::findOrFail($id)->delete();
        return redirect()->route('admin.promotions.index')->with('success', 'تم حذف العرض بنجاح');
    }
}
