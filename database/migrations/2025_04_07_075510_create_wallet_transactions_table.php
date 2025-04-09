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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->comment('deposit, withdraw, payment, refund');
            $table->decimal('amount', 12, 0);
            $table->decimal('balance_before', 12, 0);
            $table->decimal('balance_after', 12, 0);
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable()->comment('ID của đơn hàng, giao dịch nạp tiền, v.v.');
            $table->string('reference_type')->nullable()->comment('Order, BoostingOrder, Deposit');
            $table->string('status')->default('completed');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
