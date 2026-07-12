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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['payment', 'refund', 'disbursement', 'platform_fee', 'topup', 'withdraw'])->change();
            $table->enum('status', ['pending', 'success', 'failed', 'completed'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['payment', 'refund', 'disbursement', 'platform_fee'])->change();
            $table->enum('status', ['pending', 'success', 'failed'])->change();
        });
    }
};
