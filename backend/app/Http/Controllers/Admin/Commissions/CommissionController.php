<?php

namespace App\Http\Controllers\Admin\Commissions;

use App\Http\Controllers\Controller;
use App\Models\Commission\MarketerCommission;
use App\Models\User;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = MarketerCommission::with(['payment', 'marketer', 'store', 'keeper'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCommissions = $commissions->sum('commission_amount');

        return view('admin.commissions.index', compact('commissions', 'totalCommissions'));
    }

    public function settings()
    {
        $marketers = User::where('role_id', 3)->get();
        return view('admin.commissions.settings', compact('marketers'));
    }

    public function updateRate($id, \Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100'
        ]);

        $marketer = User::findOrFail($id);
        $marketer->update(['commission_rate' => $validated['commission_rate']]);

        return redirect()->back()->with('success', 'تم تحديث نسبة العمولة بنجاح');
    }
}
