<?php

namespace App\Http\Controllers\Warehouse\Payments;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Models\Payment\StorePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentConfirmationController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $pendingPayments = StorePayment::with(['store', 'marketer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedPayments = StorePayment::with(['store', 'marketer', 'keeper'])
            ->where('status', 'approved')
            ->orderBy('confirmed_at', 'desc')
            ->get();

        return view('warehouse.payments.index', compact('pendingPayments', 'approvedPayments'));
    }

    public function show($id)
    {
        $payment = StorePayment::with(['store', 'marketer', 'keeper'])->findOrFail($id);
        return view('warehouse.payments.show', compact('payment'));
    }

    public function confirm(Request $request, $paymentId)
    {
        $validated = $request->validate([
            'receipt_image' => 'required|image|max:5120'
        ]);

        try {
            $payment = $this->paymentService->confirmPayment(
                $paymentId,
                Auth::id(),
                $request->file('receipt_image')
            );

            return redirect()->route('warehouse.payments.index')
                ->with('success', 'تم توثيق إيصال القبض وخصم الدين بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject($paymentId)
    {
        try {
            $this->paymentService->rejectPayment($paymentId);
            return redirect()->route('warehouse.payments.index')
                ->with('success', 'تم رفض إيصال القبض');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
