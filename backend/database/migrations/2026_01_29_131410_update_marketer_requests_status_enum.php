<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE marketer_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled', 'documented') DEFAULT 'pending'");
        
        Schema::table('delivery_confirmation', function (Blueprint $table) {
            $table->enum('status', ['documented'])->default('documented')->after('signed_image');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE marketer_requests MODIFY COLUMN status ENUM('pending', 'approved', 'waiting_documentation') DEFAULT 'pending'");
        
        Schema::table('delivery_confirmation', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
