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
        Schema::table('backings', function (Blueprint $table) {
            $table->foreignId('campaign_tier_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bakings', function (Blueprint $table) {
            $table->foreignId('campaign_tier_id')->nullable(false)->change();
        });
    }
};
