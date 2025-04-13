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
            // Thêm trường game_id sau trường game_password hoặc game_server
            if (Schema::hasColumn('game_service_orders', 'game_password')) {
                $table->string('game_id')->nullable()->after('game_password');
            } else {
                $table->string('game_id')->nullable()->after('game_server');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_service_orders', function (Blueprint $table) {
            $table->dropColumn('game_id');
        });
    }
};
