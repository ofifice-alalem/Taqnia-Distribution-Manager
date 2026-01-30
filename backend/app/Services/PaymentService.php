<?php

namespace App\Services;

use App\Models\Payment\StorePayment;
use App\Models\Debt\StoreDebtLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function createPayment($data)
    {
        return DB::transaction(function () use ($data) {
            $paymentNumber = $this->generatePaymentNumber();

            $payment = StorePayment::create([
                'payment_number' => $paymentNumber,
                'store_id' => $data['store_id'],
                'marketer_id' => $data['marketer_id'],
                'keeper_id' => null,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
                'receipt_image' => null
            ]);

            return $payment;
        });
    }

    public function confirmPayment($paymentId, $keeperId, $receiptImage)
    {
        return DB::transaction(function () use ($paymentId, $keeperId, $receiptImage) {
            $payment = StorePayment::findOrFail($paymentId);

            if ($payment->status !== 'pending') {
                throw new \Exception('هذا الإيصال تم توثيقه مسبقاً');
            }

            $imagePath = $receiptImage->store('receipts', 'public');

            $payment->update([
                'keeper_id' => $keeperId,
                'receipt_image' => $imagePath,
                'status' => 'approved',
                'confirmed_at' => now()
            ]);

            StoreDebtLedger::create([
                'store_id' => $payment->store_id,
                'entry_type' => 'payment',
                'sales_invoice_id' => null,
                'return_id' => null,
                'payment_id' => $payment->id,
                'amount' => -$payment->amount
            ]);

            $marketer = \App\Models\User::find($payment->marketer_id);
            $commissionRate = $marketer->commission_rate ?? 0;
            $commissionAmount = ($payment->amount * $commissionRate) / 100;

            \App\Models\Commission\MarketerCommission::create([
                'payment_id' => $payment->id,
                'marketer_id' => $payment->marketer_id,
                'store_id' => $payment->store_id,
                'keeper_id' => $keeperId,
                'payment_amount' => $payment->amount,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount
            ]);

            return $payment;
        });
    }

    public function rejectPayment($paymentId)
    {
        $payment = StorePayment::findOrFail($paymentId);

        if ($payment->status !== 'pending') {
            throw new \Exception('لا يمكن رفض هذا الإيصال');
        }

        $payment->update(['status' => 'rejected']);

        return $payment;
    }

    private function generatePaymentNumber()
    {
        $lastPayment = StorePayment::orderBy('id', 'desc')->first();
        $number = $lastPayment ? intval(substr($lastPayment->payment_number, 4)) + 1 : 1;
        return 'PAY-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
