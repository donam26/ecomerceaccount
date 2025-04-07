<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('permissions')->nullable();
            $table->timestamps();
        });

        // Tạo sẵn các role cơ bản
        DB::table('roles')->insert([
            ['name' => 'Quản trị viên', 'slug' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Người dùng', 'slug' => 'user', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
