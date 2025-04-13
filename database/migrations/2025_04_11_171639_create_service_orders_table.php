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
        Schema::create('game_service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('game_service_id')->constrained();
            $table->foreignId('game_service_package_id')->nullable()->constrained('game_service_packages');
            $table->string('order_number')->unique();
            $table->decimal('amount', 12, 0);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            
            // Thông tin tài khoản game
            $table->string('game_username')->nullable();
            $table->string('game_password')->nullable();
            $table->string('game_server')->nullable();
            
            // Thông tin bổ sung
            $table->text('notes')->nullable();
            $table->json('account_details')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_service_orders');
    }
}; 