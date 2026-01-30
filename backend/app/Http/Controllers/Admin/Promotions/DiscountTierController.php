<?php

namespace App\Http\Controllers\Admin\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Promotion\InvoiceDiscountTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountTierController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'min_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
        ];
        
        if ($request->discount_type === 'percentage') {
            $rules['discount_percentage'] = 'required|numeric|min:0|max:100';
        } else {
            $rules['discount_amount'] = 'required|numeric|min:0';
        }
        
        $request->validate($rules);

        InvoiceDiscountTier::create([
            'min_amount' => $request->min_amount,
            'discount_type' => $request->discount_type,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.promotions.index')->with('success', 'تم إضافة شريحة التخفيض بنجاح');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'min_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
        ];
        
        if ($request->discount_type === 'percentage') {
            $rules['discount_percentage'] = 'required|numeric|min:0|max:100';
        } else {
            $rules['discount_amount'] = 'required|numeric|min:0';
        }
        
        $request->validate($rules);

        $tier = InvoiceDiscountTier::findOrFail($id);
        $tier->update([
            'min_amount' => $request->min_amount,
            'discount_type' => $request->discount_type,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
        ]);

        return redirect()->route('admin.promotions.index')->with('success', 'تم تحديث الشريحة بنجاح');
    }

    public function toggleStatus($id)
    {
        $tier = InvoiceDiscountTier::findOrFail($id);
        $tier->update(['is_active' => !$tier->is_active]);

        return redirect()->route('admin.promotions.index')->with('success', 'تم تحديث حالة الشريحة');
    }

    public function destroy($id)
    {
        $tier = InvoiceDiscountTier::findOrFail($id);
        $tier->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'تم حذف الشريحة بنجاح');
    }
}
