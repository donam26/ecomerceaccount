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
            // Sửa cột order_id để cho phép NULL
            $table->foreignId('order_id')->nullable()->change();
            
            // Thêm cột boosting_order_id cho giao dịch đơn hàng cày thuê
            $table->foreignId('boosting_order_id')->nullable()->after('order_id');
            
            // Thêm khóa ngoại cho boosting_order_id
            $table->foreign('boosting_order_id')
                ->references('id')
                ->on('boosting_orders')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Xóa khóa ngoại
            $table->dropForeign(['boosting_order_id']);
            
            // Xóa cột
            $table->dropColumn('boosting_order_id');
            
            // Đặt lại order_id thành NOT NULL
            $table->foreignId('order_id')->nullable(false)->change();
        });
    }
};
