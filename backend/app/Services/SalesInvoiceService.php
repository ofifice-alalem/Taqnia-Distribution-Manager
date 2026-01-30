<?php

namespace App\Services;

use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceItem;
use App\Models\Stock\MarketerActualStock;
use App\Models\Stock\StorePendingStock;
use App\Models\Product;
use App\Models\Promotion\ProductPromotion;
use App\Models\Promotion\InvoiceDiscountTier;
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
                'subtotal' => 0,
                'product_discount' => 0,
                'invoice_discount_type' => null,
                'invoice_discount_value' => 0,
                'invoice_discount_amount' => 0,
                'total_amount' => 0,
                'status' => 'pending',
            ]);

            $subtotal = 0;
            $product_discount = 0;

            // إضافة المنتجات
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $free_quantity = $item['free_quantity'] ?? 0;
                $promotion_id = $item['promotion_id'] ?? null;
                
                $unit_price = $product->current_price;
                $total_quantity = $quantity + $free_quantity;
                
                // حساب السعر
                $item_subtotal = $total_quantity * $unit_price;
                $item_discount = $free_quantity * $unit_price;
                $total_price = $quantity * $unit_price;
                
                $subtotal += $item_subtotal;
                $product_discount += $item_discount;

                // حفظ تفاصيل الفاتورة
                SalesInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'free_quantity' => $free_quantity,
                    'promotion_id' => $promotion_id,
                    'unit_price' => $unit_price,
                    'total_price' => $total_price,
                ]);

                // خصم من مخزون المسوق (الكمية الكلية)
                MarketerActualStock::where('marketer_id', $marketer_id)
                    ->where('product_id', $item['product_id'])
                    ->decrement('quantity', $total_quantity);

                // إضافة للمخزون المرحلي (الكمية الكلية)
                StorePendingStock::create([
                    'store_id' => $store_id,
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $total_quantity,
                ]);
            }

            // حساب التخفيض على الفاتورة تلقائياً
            $after_product_discount = $subtotal - $product_discount;
            $invoice_discount_amount = 0;
            $invoice_discount_value = 0;
            $invoice_discount_type = null;
            
            // جلب التخفيض المناسب بناءً على المبلغ
            $tier = InvoiceDiscountTier::where('is_active', true)
                ->where('min_amount', '<=', $after_product_discount)
                ->orderBy('min_amount', 'desc')
                ->first();
            
            if ($tier) {
                $invoice_discount_type = $tier->discount_type;
                
                if ($tier->discount_type === 'percentage') {
                    $invoice_discount_value = $tier->discount_percentage;
                    $invoice_discount_amount = ($after_product_discount * $tier->discount_percentage) / 100;
                } else {
                    $invoice_discount_value = $tier->discount_amount;
                    $invoice_discount_amount = $tier->discount_amount;
                }
            }
            
            $total_amount = $after_product_discount - $invoice_discount_amount;

            // تحديث الفاتورة
            $invoice->update([
                'subtotal' => $subtotal,
                'product_discount' => $product_discount,
                'invoice_discount_type' => $invoice_discount_type,
                'invoice_discount_value' => $invoice_discount_value,
                'invoice_discount_amount' => $invoice_discount_amount,
                'total_amount' => $total_amount
            ]);

            return $invoice->load('items.product', 'store', 'marketer');
        });
    }

    private function generateInvoiceNumber()
    {
        return 'SI-' . date('Ymd') . '-' . str_pad(SalesInvoice::count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
