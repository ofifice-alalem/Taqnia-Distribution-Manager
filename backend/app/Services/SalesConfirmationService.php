<?php

namespace App\Services;

use App\Models\Sales\SalesInvoice;
use App\Models\Stock\StorePendingStock;
use App\Models\Stock\StoreActualStock;
use App\Models\Debt\StoreDebtLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SalesConfirmationService
{
    public function confirmSale(int $invoiceId, int $keeperId, $stampedImage)
    {
        return DB::transaction(function () use ($invoiceId, $keeperId, $stampedImage) {
            $invoice = SalesInvoice::with('items')->findOrFail($invoiceId);

            if ($invoice->status !== 'pending') {
                throw new \Exception("الفاتورة ليست في حالة انتظار");
            }

            // رفع الصورة
            $imagePath = $stampedImage->store('sales_invoices', 'public');

            // تحديث الفاتورة
            $invoice->update([
                'status' => 'approved',
                'keeper_id' => $keeperId,
                'stamped_invoice_image' => $imagePath,
                'confirmed_at' => now(),
            ]);

            // نقل المخزون من المرحلي إلى الفعلي
            foreach ($invoice->items as $item) {
                // حذف من المخزون المرحلي
                StorePendingStock::where('sales_invoice_id', $invoiceId)
                    ->where('product_id', $item->product_id)
                    ->delete();

                // إضافة للمخزون الفعلي
                $actualStock = StoreActualStock::where('store_id', $invoice->store_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($actualStock) {
                    $actualStock->increment('quantity', $item->quantity);
                } else {
                    StoreActualStock::create([
                        'store_id' => $invoice->store_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ]);
                }
            }

            // تسجيل الدين
            StoreDebtLedger::create([
                'store_id' => $invoice->store_id,
                'entry_type' => 'sale',
                'sales_invoice_id' => $invoiceId,
                'amount' => $invoice->total_amount,
            ]);

            return $invoice->fresh(['items.product', 'store', 'marketer', 'keeper']);
        });
    }
}
