<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal\MarketerWithdrawalRequest;
use App\Models\Withdrawal\MarketerWithdrawal;
use App\Models\Commission\MarketerCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminWithdrawalController extends Controller
{
    // عرض صفحة طلبات السحب للمسؤول
    public function index()
    {
        $requests = MarketerWithdrawalRequest::with('marketer')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.withdrawals.index', compact('requests'));
    }
    
    // عرض تفاصيل طلب سحب
    public function show($id)
    {
        $request = MarketerWithdrawalRequest::with('marketer')->findOrFail($id);
        
        // حساب رصيد المسوق
        $totalEarned = MarketerCommission::where('marketer_id', $request->marketer_id)
            ->sum('commission_amount');
        $totalWithdrawn = MarketerWithdrawal::where('marketer_id', $request->marketer_id)
            ->sum('amount');
        $availableBalance = $totalEarned - $totalWithdrawn;
        
        return view('admin.withdrawals.show', compact('request', 'totalEarned', 'totalWithdrawn', 'availableBalance'));
    }
    
    // رفض طلب سحب
    public function reject($id)
    {
        $request = MarketerWithdrawalRequest::findOrFail($id);
        
        if ($request->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض هذا الطلب');
        }
        
        $request->update(['status' => 'rejected']);
        
        return back()->with('success', 'تم رفض الطلب');
    }
    
    // الموافقة وتسليم المبلغ
    public function approve(Request $request, $id)
    {
        $request->validate([
            'signed_receipt_image' => 'required|image|max:2048'
        ]);
        
        $withdrawalRequest = MarketerWithdrawalRequest::findOrFail($id);
        
        if ($withdrawalRequest->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }
        
        // التحقق من الرصيد
        $totalEarned = MarketerCommission::where('marketer_id', $withdrawalRequest->marketer_id)
            ->sum('commission_amount');
        $totalWithdrawn = MarketerWithdrawal::where('marketer_id', $withdrawalRequest->marketer_id)
            ->sum('amount');
        $availableBalance = $totalEarned - $totalWithdrawn;
        
        if ($withdrawalRequest->requested_amount > $availableBalance) {
            return back()->with('error', 'رصيد المسوق غير كافٍ');
        }
        
        // رفع الصورة
        $imagePath = $request->file('signed_receipt_image')->store('withdrawals', 'public');
        
        // تحديث حالة الطلب
        $withdrawalRequest->update(['status' => 'approved']);
        
        // إنشاء سجل السحب الموثق
        MarketerWithdrawal::create([
            'withdrawal_request_id' => $withdrawalRequest->id,
            'marketer_id' => $withdrawalRequest->marketer_id,
            'amount' => $withdrawalRequest->requested_amount,
            'admin_id' => Auth::id(),
            'signed_receipt_image' => $imagePath,
            'confirmed_at' => now()
        ]);
        
        return back()->with('success', 'تم الموافقة وتوثيق السحب بنجاح');
    }
}
