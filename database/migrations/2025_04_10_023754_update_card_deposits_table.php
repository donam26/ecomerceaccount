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
        Schema::table('card_deposits', function (Blueprint $table) {
            // Thêm trường metadata
            $table->text('metadata')->nullable()->comment('Dữ liệu bổ sung')->after('response');
            
            // Cập nhật loại dữ liệu của amount và actual_amount để đồng nhất với wallet_deposits
            $table->decimal('amount', 12, 0)->change();
            $table->decimal('actual_amount', 12, 0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card_deposits', function (Blueprint $table) {
            $table->dropColumn('metadata');
            $table->decimal('amount', 12, 2)->change();
            $table->decimal('actual_amount', 12, 2)->change();
        });
    }
};
