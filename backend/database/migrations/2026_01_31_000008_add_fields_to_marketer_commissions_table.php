<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketer_commissions', function (Blueprint $table) {
            $table->foreignId('store_id')->after('marketer_id')->constrained('stores');
            $table->foreignId('keeper_id')->after('store_id')->constrained('users');
            $table->decimal('payment_amount', 12, 2)->after('keeper_id');
        });
    }

    public function down(): void
    {
        Schema::table('marketer_commissions', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropForeign(['keeper_id']);
            $table->dropColumn(['store_id', 'keeper_id', 'payment_amount']);
        });
    }
};
