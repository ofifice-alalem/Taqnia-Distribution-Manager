<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_discount_tiers', function (Blueprint $table) {
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage')->after('min_amount');
            $table->decimal('discount_amount', 12, 2)->nullable()->after('discount_percentage');
            $table->decimal('discount_percentage', 5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_discount_tiers', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_amount']);
        });
    }
};
