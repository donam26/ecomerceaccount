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
        Schema::create('card_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('telco')->comment('Nhà mạng (VIETTEL, MOBIFONE, VINAPHONE, ...)');
            $table->decimal('amount', 12, 2)->comment('Mệnh giá thẻ');
            $table->string('serial')->comment('Số serial thẻ cào');
            $table->string('code')->comment('Mã thẻ cào');
            $table->string('request_id')->unique()->comment('Mã yêu cầu gửi đến API');
            $table->string('trans_id')->nullable()->comment('Mã giao dịch từ nhà cung cấp');
            $table->string('status')->default('pending')->comment('Trạng thái: pending, completed, failed');
            $table->decimal('actual_amount', 12, 2)->default(0)->comment('Số tiền thực tế được cộng vào ví');
            $table->text('response')->nullable()->comment('Phản hồi từ API');
            $table->foreignId('transaction_id')->nullable()->comment('ID giao dịch ví khi nạp thành công');
            $table->timestamp('completed_at')->nullable()->comment('Thời gian hoàn thành');
            $table->timestamps();

            $table->index('status');
            $table->index('telco');
            $table->index('request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_deposits');
    }
};
