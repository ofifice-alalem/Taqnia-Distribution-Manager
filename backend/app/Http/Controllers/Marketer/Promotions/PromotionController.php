<?php

namespace App\Http\Controllers\Marketer\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Promotion\ProductPromotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = ProductPromotion::with('product')
            ->active()
            ->orderBy('end_date', 'asc')
            ->get();

        return view('marketer.promotions.index', compact('promotions'));
    }
}
