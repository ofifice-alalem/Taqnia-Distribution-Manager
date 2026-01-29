<?php

namespace App\Services;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceItem;
use App\Models\Stock\MarketerActualStock;
use App\Models\Stock\StorePendingStock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SalesInvoiceService
{
    public function createPendingSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            $marketer_id = $data['marketer_id'];
            $store_id = $data['store_id'];
            $items = $data['items'];

            // التحقق من المخزون
            foreach ($items as $item) {
                $stock = MarketerActualStock::where('marketer_id', $marketer_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$stock || $stock->quantity < $item['quantity']) {
                    throw new \Exception("مخزون غير كافٍ للمنتج ID: {$item['product_id']}");
                }
            }

            // إنشاء الفاتورة
            $invoice = SalesInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'marketer_id' => $marketer_id,
                'store_id' => $store_id,
                'total_amount' => 0,
                'status' => 'pending',
            ]);

            $total = 0;

            // إضافة المنتجات
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unit_price = $product->current_price;
                $total_price = $unit_price * $item['quantity'];
                $total += $total_price;

                // حفظ تفاصيل الفاتورة
                SalesInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unit_price,
                    'total_price' => $total_price,
                ]);

                // خصم من مخزون المسوق
                MarketerActualStock::where('marketer_id', $marketer_id)
                    ->where('product_id', $item['product_id'])
                    ->decrement('quantity', $item['quantity']);

                // إضافة للمخزون المرحلي
                StorePendingStock::create([
                    'store_id' => $store_id,
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // تحديث المبلغ الإجمالي
            $invoice->update(['total_amount' => $total]);

            return $invoice->load('items.product', 'store', 'marketer');
        });
    }

    private function generateInvoiceNumber()
    {
        return 'SI-' . date('Ymd') . '-' . str_pad(SalesInvoice::count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
