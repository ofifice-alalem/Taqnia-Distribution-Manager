<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal\MarketerWithdrawalRequest;
use App\Models\Withdrawal\MarketerWithdrawal;
use App\Models\Commission\MarketerCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketerWithdrawalController extends Controller
{
    // عرض صفحة طلبات السحب للمسوق
    public function index()
    {
        $marketerId = Auth::id();
        
        // حساب الرصيد
        $totalEarned = MarketerCommission::where('marketer_id', $marketerId)->sum('commission_amount');
        $totalWithdrawn = MarketerWithdrawal::where('marketer_id', $marketerId)->sum('amount');
        $availableBalance = $totalEarned - $totalWithdrawn;
        
        // طلبات السحب
        $requests = MarketerWithdrawalRequest::where('marketer_id', $marketerId)
            ->whereIn('status', ['pending', 'rejected', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // السحوبات الموثقة
        $withdrawals = MarketerWithdrawal::where('marketer_id', $marketerId)
            ->with(['admin', 'request'])
            ->orderBy('confirmed_at', 'desc')
            ->get();
        
        return view('marketer.withdrawals.index', compact(
            'totalEarned',
            'totalWithdrawn',
            'availableBalance',
            'requests',
            'withdrawals'
        ));
    }
    
    // إنشاء طلب سحب جديد
    public function store(Request $request)
    {
        $request->validate([
            'requested_amount' => 'required|numeric|min:1'
        ]);
        
        $marketerId = Auth::id();
        
        // التحقق من الرصيد
        $totalEarned = MarketerCommission::where('marketer_id', $marketerId)->sum('commission_amount');
        $totalWithdrawn = MarketerWithdrawal::where('marketer_id', $marketerId)->sum('amount');
        $availableBalance = $totalEarned - $totalWithdrawn;
        
        if ($request->requested_amount > $availableBalance) {
            return back()->with('error', 'الرصيد المتاح غير كافٍ');
        }
        
        MarketerWithdrawalRequest::create([
            'marketer_id' => $marketerId,
            'requested_amount' => $request->requested_amount,
            'status' => 'pending'
        ]);
        
        return back()->with('success', 'تم إرسال طلب السحب بنجاح');
    }
    
    // إلغاء طلب سحب معلق
    public function cancel($id)
    {
        $request = MarketerWithdrawalRequest::where('id', $id)
            ->where('marketer_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();
        
        $request->update(['status' => 'cancelled']);
        
        return back()->with('success', 'تم إلغاء الطلب');
    }
    
    // طباعة طلب السحب
    public function print($id)
    {
        $request = MarketerWithdrawalRequest::with('marketer')
            ->where('marketer_id', Auth::id())
            ->findOrFail($id);
        
        // حساب الرصيد
        $totalEarned = MarketerCommission::where('marketer_id', $request->marketer_id)
            ->sum('commission_amount');
        $totalWithdrawn = MarketerWithdrawal::where('marketer_id', $request->marketer_id)
            ->sum('amount');
        $availableBalance = $totalEarned - $totalWithdrawn;
        
        $arabic = new \ArPHP\I18N\Arabic();
        
        $statusText = match($request->status) {
            'pending' => 'في الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغى',
            default => 'غير محدد'
        };
        
        $data = [
            'requestNumber' => $request->id,
            'date' => \Carbon\Carbon::parse($request->created_at)->format('Y-m-d H:i'),
            'marketerName' => $arabic->utf8Glyphs($request->marketer->full_name ?? 'غير محدد'),
            'requestedAmount' => number_format($request->requested_amount, 2),
            'totalEarned' => number_format($totalEarned, 2),
            'totalWithdrawn' => number_format($totalWithdrawn, 2),
            'availableBalance' => number_format($availableBalance, 2),
            'remaining' => number_format($availableBalance - $request->requested_amount, 2),
            'status' => $arabic->utf8Glyphs($statusText),
            'statusCode' => $request->status,
            'title' => $arabic->utf8Glyphs('طلب سحب أرباح'),
            'labels' => [
                'requestNumber' => $arabic->utf8Glyphs('رقم الطلب'),
                'date' => $arabic->utf8Glyphs('تاريخ الطلب'),
                'marketer' => $arabic->utf8Glyphs('اسم المسوق'),
                'requestedAmount' => $arabic->utf8Glyphs('المبلغ المطلوب'),
                'totalEarned' => $arabic->utf8Glyphs('إجمالي الأرباح'),
                'totalWithdrawn' => $arabic->utf8Glyphs('إجمالي المسحوب'),
                'availableBalance' => $arabic->utf8Glyphs('الرصيد المتاح'),
                'remaining' => $arabic->utf8Glyphs('المتبقي'),
                'status' => $arabic->utf8Glyphs('حالة الطلب'),
                'currency' => $arabic->utf8Glyphs('ريال'),
                'marketerSign' => $arabic->utf8Glyphs('توقيع المسوق'),
                'adminSign' => $arabic->utf8Glyphs('توقيع المسؤول'),
                'cancelledNote' => $arabic->utf8Glyphs('الطلب ملغى ولا يعتد به'),
            ]
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('marketer.withdrawals.print', $data)->setPaper('a4');
        
        return $pdf->download('withdrawal-request-' . $request->id . '.pdf');
    }
}
