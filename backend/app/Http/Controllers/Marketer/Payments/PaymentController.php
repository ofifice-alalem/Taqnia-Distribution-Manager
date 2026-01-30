<?php

namespace App\Http\Controllers\Marketer\Payments;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Models\Payment\StorePayment;
use App\Models\Store\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $marketerId = Auth::id();

        $pendingPayments = StorePayment::with(['store', 'marketer'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedPayments = StorePayment::with(['store', 'marketer', 'keeper'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->orderBy('confirmed_at', 'desc')
            ->get();

        $cancelledPayments = StorePayment::with(['store', 'marketer'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('marketer.payments.index', compact('pendingPayments', 'approvedPayments', 'cancelledPayments'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)
            ->get()
            ->map(function($store) {
                $store->total_debt = $store->total_debt;
                return $store;
            })
            ->filter(function($store) {
                return $store->total_debt > 0;
            });
        
        $currentRate = Auth::user()->commission_rate ?? 0;
            
        return view('marketer.payments.create', compact('stores', 'currentRate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,transfer,certified_check'
        ]);

        try {
            $validated['marketer_id'] = Auth::id();
            $payment = $this->paymentService->createPayment($validated);

            return redirect()->route('marketer.payments.show', $payment->id)
                ->with('success', 'تم إنشاء إيصال القبض بنجاح. في انتظار التوثيق من أمين المخزن');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $payment = StorePayment::with(['store', 'marketer', 'keeper'])
            ->where('marketer_id', Auth::id())
            ->findOrFail($id);

        $commission = \App\Models\Commission\MarketerCommission::where('payment_id', $id)->first();
        $currentRate = Auth::user()->commission_rate ?? 0;
        $expectedProfit = ($payment->amount * $currentRate) / 100;

        return view('marketer.payments.show', compact('payment', 'commission', 'currentRate', 'expectedProfit'));
    }

    public function cancel($id)
    {
        try {
            $payment = StorePayment::where('marketer_id', Auth::id())
                ->where('status', 'pending')
                ->findOrFail($id);

            $payment->update(['status' => 'cancelled']);

            return redirect()->route('marketer.payments.index')
                ->with('success', 'تم إلغاء الإيصال بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء الإيصال');
        }
    }

    public function print($id)
    {
        $payment = StorePayment::with(['store', 'marketer', 'keeper'])
            ->where('marketer_id', Auth::id())
            ->findOrFail($id);

        $arabic = new \ArPHP\I18N\Arabic();

        $paymentMethodAr = [
            'cash' => 'كاش',
            'transfer' => 'حوالة',
            'certified_check' => 'شيك مصدق'
        ];

        $data = [
            'paymentNumber' => $payment->payment_number,
            'date' => $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : '-',
            'marketerName' => $arabic->utf8Glyphs($payment->marketer->full_name),
            'storeName' => $arabic->utf8Glyphs($payment->store->name),
            'storeOwner' => $arabic->utf8Glyphs($payment->store->owner_name ?? '-'),
            'amount' => $payment->amount,
            'paymentMethod' => $arabic->utf8Glyphs($paymentMethodAr[$payment->payment_method] ?? '-'),
            'keeperName' => $payment->keeper ? $arabic->utf8Glyphs($payment->keeper->full_name) : null,
            'confirmedAt' => $payment->confirmed_at ? $payment->confirmed_at->format('Y-m-d H:i') : null,
            'cancelled' => $payment->status == 'cancelled',
            'cancelledText' => $arabic->utf8Glyphs('الإيصال ملغى ولا يعتد به'),
            'title' => $arabic->utf8Glyphs('إيصال قبض'),
            'labels' => [
                'paymentNumber' => $arabic->utf8Glyphs('رقم الإيصال'),
                'date' => $arabic->utf8Glyphs('التاريخ'),
                'marketer' => $arabic->utf8Glyphs('المسوق'),
                'store' => $arabic->utf8Glyphs('المتجر'),
                'owner' => $arabic->utf8Glyphs('المالك'),
                'keeper' => $arabic->utf8Glyphs('أمين المخزن'),
                'amount' => $arabic->utf8Glyphs('المبلغ المدفوع'),
                'paymentMethod' => $arabic->utf8Glyphs('طريقة الدفع'),
                'confirmedAt' => $arabic->utf8Glyphs('تاريخ التوثيق'),
                'currency' => $arabic->utf8Glyphs('ريال'),
                'marketerSignature' => $arabic->utf8Glyphs('توقيع المسوق'),
                'keeperSignature' => $arabic->utf8Glyphs('توقيع أمين المخزن'),
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('marketer.payments.receipt-pdf', $data)->setPaper('a4');
        return $pdf->download('payment-' . $payment->payment_number . '.pdf');
    }

    public function received()
    {
        $marketerId = Auth::id();

        $receivedPayments = StorePayment::with(['store', 'marketer', 'keeper'])
            ->where('marketer_id', $marketerId)
            ->where('status', 'approved')
            ->orderBy('confirmed_at', 'desc')
            ->get();

        $totalReceived = $receivedPayments->sum('amount');

        return view('marketer.payments.received', compact('receivedPayments', 'totalReceived'));
    }

    public function myProfits()
    {
        $marketerId = Auth::id();
        $marketer = \App\Models\User::find($marketerId);

        $commissions = \App\Models\Commission\MarketerCommission::with(['payment', 'store', 'keeper'])
            ->where('marketer_id', $marketerId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCommissions = $commissions->sum('commission_amount');
        $currentRate = $marketer->commission_rate ?? 0;

        return view('marketer.payments.profits', compact('commissions', 'totalCommissions', 'currentRate'));
    }
}
