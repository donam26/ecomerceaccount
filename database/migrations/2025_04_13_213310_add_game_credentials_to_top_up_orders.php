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
        Schema::table('top_up_orders', function (Blueprint $table) {
            $table->string('game_username')->nullable()->after('game_id');
            $table->string('game_password')->nullable()->after('game_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('top_up_orders', function (Blueprint $table) {
            $table->dropColumn(['game_username', 'game_password']);
        });
    }
};
