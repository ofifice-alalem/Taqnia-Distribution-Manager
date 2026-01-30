<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(0)->after('total_amount');
            $table->decimal('product_discount', 12, 2)->default(0)->after('subtotal');
            $table->enum('invoice_discount_type', ['percentage', 'fixed'])->nullable()->after('product_discount');
            $table->decimal('invoice_discount_value', 10, 2)->nullable()->after('invoice_discount_type');
            $table->decimal('invoice_discount_amount', 12, 2)->default(0)->after('invoice_discount_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'product_discount',
                'invoice_discount_type',
                'invoice_discount_value',
                'invoice_discount_amount'
            ]);
        });
    }
};
