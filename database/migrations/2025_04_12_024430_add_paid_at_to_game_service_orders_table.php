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
        Schema::table('game_service_orders', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('payment_method')->comment('Thời gian thanh toán');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_service_orders', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });
    }
};
