<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion\InvoiceDiscountTier;
use Illuminate\Http\Request;

class DiscountCalculatorController extends Controller
{
    public function calculate(Request $request)
    {
        $amount = $request->get('amount', 0);
        
        $tier = InvoiceDiscountTier::where('is_active', true)
            ->where('min_amount', '<=', $amount)
            ->orderBy('min_amount', 'desc')
            ->first();
        
        $discount = 0;
        
        if ($tier) {
            if ($tier->discount_type === 'percentage') {
                $discount = ($amount * $tier->discount_percentage) / 100;
            } else {
                $discount = $tier->discount_amount;
            }
        }
        
        return response()->json([
            'discount' => $discount,
            'tier' => $tier ? [
                'type' => $tier->discount_type,
                'value' => $tier->discount_type === 'percentage' ? $tier->discount_percentage : $tier->discount_amount
            ] : null
        ]);
    }
}
