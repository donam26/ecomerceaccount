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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Tạo các vai trò
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Quản trị viên']
        );
        
        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'Người dùng']
        );
        
        // Tạo tài khoản admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );
        
        // Tạo tài khoản người dùng mẫu
        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Nguyễn Văn A',
                'password' => Hash::make('password'),
                'role_id' => $userRole->id,
            ]
        );
        
        // Tạo danh sách game
        $game1 = Game::firstOrCreate(
            ['name' => 'Liên Quân Mobile'],
            [
                'slug' => 'lien-quan-mobile',
                'description' => 'Liên Quân Mobile là một trò chơi đấu trường trận chiến trực tuyến nhiều người chơi (MOBA) được phát triển và phát hành bởi Garena.',
                'thumbnail' => 'games/lien-quan-thumb.jpg',
                'banner_image' => 'games/lien-quan-banner.jpg',
                'is_active' => true,
                'display_order' => 1,
            ]
        );
        
        $game2 = Game::firstOrCreate(
            ['name' => 'Free Fire'],
            [
                'slug' => 'free-fire',
                'description' => 'Garena Free Fire là một trò chơi bắn súng góc nhìn thứ ba sinh tồn do 111 Dots Studio phát triển và Garena phát hành.',
                'thumbnail' => 'games/free-fire-thumb.jpg',
                'banner_image' => 'games/free-fire-banner.jpg', 
                'is_active' => true,
                'display_order' => 2,
            ]
        );
        
        $game3 = Game::firstOrCreate(
            ['name' => 'PUBG Mobile'],
            [
                'slug' => 'pubg-mobile',
                'description' => 'PUBG Mobile là một trò chơi battle royale miễn phí dành cho thiết bị di động được phát triển bởi LightSpeed & Quantum Studio.',
                'thumbnail' => 'games/pubg-thumb.jpg',
                'banner_image' => 'games/pubg-banner.jpg',
                'is_active' => true,
                'display_order' => 3,
            ]
        );
        
        // Tạo tài khoản game mẫu cho Liên Quân
        Account::firstOrCreate(
            ['title' => 'Tài khoản Liên Quân VIP 100 tướng + 50 skin'],
            [
                'game_id' => $game1->id,
                'description' => 'Tài khoản Liên Quân cực VIP với 100 tướng đã mở, 50 skin hiếm, nhiều trang phục giới hạn.',
                'price' => 2000000,
                'original_price' => 3000000,
                'status' => 'available',
                'attributes' => json_encode([
                    'level' => 30,
                    'rank' => 'Cao Thủ',
                    'heroes_count' => 100,
                    'skins_count' => 50,
                    'gems' => 10000,
                ]),
                'images' => json_encode([
                    'accounts/lienquan1.jpg',
                    'accounts/lienquan2.jpg',
                    'accounts/lienquan3.jpg',
                ]),
                'is_featured' => true,
            ]
        );
        
        Account::firstOrCreate(
            ['title' => 'Tài khoản Liên Quân 50 tướng rank Kim Cương'],
            [
                'game_id' => $game1->id,
                'description' => 'Tài khoản Liên Quân với 50 tướng, rank Kim Cương, nhiều skin đẹp.',
                'price' => 800000,
                'original_price' => 1200000,
                'status' => 'available',
                'attributes' => json_encode([
                    'level' => 25,
                    'rank' => 'Kim Cương',
                    'heroes_count' => 50,
                    'skins_count' => 20,
                    'gems' => 5000,
                ]),
                'images' => json_encode([
                    'accounts/lienquan4.jpg',
                    'accounts/lienquan5.jpg',
                    'accounts/lienquan6.jpg',
                ]),
                'is_featured' => true,
            ]
        );
        
        // Tạo tài khoản game mẫu cho Free Fire
        Account::firstOrCreate(
            ['title' => 'Tài khoản Free Fire full súng ngầu'],
            [
                'game_id' => $game2->id,
                'description' => 'Tài khoản Free Fire với đầy đủ súng và trang phục ngầu, nhiều skin hiếm.',
                'price' => 1500000,
                'original_price' => 2000000,
                'status' => 'available',
                'attributes' => json_encode([
                    'level' => 60,
                    'rank' => 'Huyền Thoại',
                    'skins_count' => 30,
                    'diamonds' => 5000,
                ]),
                'images' => json_encode([
                    'accounts/freefire1.jpg',
                    'accounts/freefire2.jpg',
                    'accounts/freefire3.jpg',
                ]),
                'is_featured' => true,
            ]
        );
        
        // Tạo tài khoản game mẫu cho PUBG
        Account::firstOrCreate(
            ['title' => 'Tài khoản PUBG Mobile full set trang phục'],
            [
                'game_id' => $game3->id,
                'description' => 'Tài khoản PUBG Mobile với nhiều trang phục giới hạn, súng skin đẹp.',
                'price' => 1200000,
                'original_price' => 1800000,
                'status' => 'available',
                'attributes' => json_encode([
                    'level' => 70,
                    'rank' => 'Conqueror',
                    'skins_count' => 25,
                    'uc' => 10000,
                ]),
                'images' => json_encode([
                    'accounts/pubg1.jpg',
                    'accounts/pubg2.jpg',
                    'accounts/pubg3.jpg',
                ]),
                'is_featured' => true,
            ]
        );
    }
}
