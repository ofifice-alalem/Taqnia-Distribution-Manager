<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketer_withdrawals', function (Blueprint $table) {
            $table->dropForeign(['keeper_id']);
            $table->renameColumn('keeper_id', 'admin_id');
        });
        
        Schema::table('marketer_withdrawals', function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('marketer_withdrawals', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->renameColumn('admin_id', 'keeper_id');
        });
        
        Schema::table('marketer_withdrawals', function (Blueprint $table) {
            $table->foreign('keeper_id')->references('id')->on('users');
        });
    }
};
