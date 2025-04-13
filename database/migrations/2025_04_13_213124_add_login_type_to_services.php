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
        // Thêm trường login_type vào bảng game_services
        Schema::table('game_services', function (Blueprint $table) {
            $table->enum('login_type', ['username_password', 'game_id', 'both'])
                ->default('username_password')
                ->after('type')
                ->comment('Kiểu thông tin đăng nhập yêu cầu');
        });

        // Thêm trường login_type vào bảng topup_services
        Schema::table('top_up_services', function (Blueprint $table) {
            $table->enum('login_type', ['username_password', 'game_id', 'both'])
                ->default('game_id')
                ->after('is_active')
                ->comment('Kiểu thông tin đăng nhập yêu cầu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa trường login_type khỏi bảng game_services
        Schema::table('game_services', function (Blueprint $table) {
            $table->dropColumn('login_type');
        });

        // Xóa trường login_type khỏi bảng topup_services
        Schema::table('top_up_services', function (Blueprint $table) {
            $table->dropColumn('login_type');
        });
    }
};
