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
        Schema::create('wallet_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('deposit_code')->unique()->comment('Mã nạp tiền duy nhất (WALLET-xxxx)');
            $table->decimal('amount', 12, 0)->default(0)->comment('Số tiền nạp');
            $table->string('payment_method')->default('bank_transfer')->comment('Phương thức thanh toán');
            $table->string('status')->default('pending')->comment('Trạng thái: pending, completed, failed');
            $table->text('payment_content')->nullable()->comment('Nội dung chuyển khoản');
            $table->string('transaction_id')->nullable()->comment('ID giao dịch từ cổng thanh toán');
            $table->text('metadata')->nullable()->comment('Dữ liệu bổ sung');
            $table->timestamp('completed_at')->nullable()->comment('Thời gian hoàn thành');
            $table->timestamps();
            
            // Thêm index cho tìm kiếm
            $table->index('deposit_code');
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_deposits');
    }
};
