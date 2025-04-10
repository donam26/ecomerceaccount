<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Game;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo các vai trò
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Quản trị viên']
        );
        
        // Tạo tài khoản admin
        User::firstOrCreate(
            ['email' => 'admin@shopbuffsao.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('bichngoc'),
                'role_id' => $adminRole->id,
            ]
        );
     
    }
}
