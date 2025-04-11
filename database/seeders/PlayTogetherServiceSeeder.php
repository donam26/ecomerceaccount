<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameService;
use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class PlayTogetherServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tìm hoặc tạo game Play Together
        $game = Game::firstOrCreate(
            ['slug' => 'play-together'],
            [
                'name' => 'Play Together',
                'description' => 'Game mô phỏng đời sống Play Together',
                'status' => 'active',
            ]
        );

        // Tạo dịch vụ Thuê câu cá bóng 5 6
        $service = GameService::create([
            'game_id' => $game->id,
            'name' => 'Thuê câu cá bóng 5 6 Play Together',
            'slug' => 'thue-cau-ca-bong-5-6-play-together',
            'description' => 'Dịch vụ thuê câu cá trong game Play Together, nhận tất cả cá bóng 5 6 câu được',
            'image' => '/images/services/fishing.jpg', // Điều chỉnh đường dẫn phù hợp
            'type' => 'fishing',
            'price' => 99000,
            'status' => 'active',
            'completed_count' => 780,
        ]);

        // Tạo các gói dịch vụ
        $packages = [
            [
                'name' => 'CÂU NGẪU NHIÊN 10 CÁ BÓNG 5 (tự nạp gói thành viên bắt tức thì) (tỉ lệ bạn acc 1%)',
                'price' => 69000,
                'display_order' => 1,
            ],
            [
                'name' => 'CÂU NGẪU NHIÊN 5 CON CÁ BÓNG 6 (tự nạp gói thành viên bắt tức thì) (tỉ lệ bạn acc 1%)',
                'price' => 80000,
                'display_order' => 2,
            ],
            [
                'name' => 'CÂU NGẪU NHIÊN 10 CON CÁ BÓNG 6 (tỉ lệ bạn acc 1%)',
                'price' => 120000,
                'display_order' => 3,
            ],
            [
                'name' => 'CÂU NGẪU NHIÊN 20 CON CÁ BÓNG 6 (tỉ lệ bạn acc 1%)',
                'price' => 179000,
                'display_order' => 4,
            ],
            [
                'name' => 'CÂU CHỈ ĐỊNH 1 CÁ BÓNG 6 KHÔNG LÔ (sẽ nhận TẤT CẢ BÓNG 6 CÂU ĐƯỢC cho tới khi ra CÁ CHỈ ĐỊNH)',
                'price' => 150000,
                'display_order' => 5,
            ],
            [
                'name' => 'CÂU CHỈ ĐỊNH 1 CÁ BÓNG 6 QUÁI VẬT TRŨ CÁ MẬP ĐỎ, LEVIATAN (sẽ nhận TẤT CẢ BÓNG 6 CÂU ĐƯỢC cho tới khi ra CÁ CHỈ ĐỊNH)',
                'price' => 180000,
                'display_order' => 6,
            ],
            [
                'name' => 'CÂU RÙA ARCHELON VƯƠNG MIỄN (sẽ nhận TẤT CẢ BÓNG 6 CÂU ĐƯỢC cho tới khi ra RÙA VƯƠNG MIỄN) (tỉ lệ bạn acc 1%)',
                'price' => 219000,
                'display_order' => 7,
            ],
            [
                'name' => 'CÁ MẬP ĐỎ HOẶC LIVYATAN (chỉ câu khi nik có cần mở neo, cần cá mập, cần quái vật hoặc cần 4-5 dòng S) (sẽ nhận TẤT CẢ BÓNG 6 CÂU ĐƯỢC cho tới khi ra CÁ CHỈ ĐỊNH) (tỉ lệ bạn acc 1%)',
                'price' => 250000,
                'display_order' => 8,
            ],
        ];

        foreach ($packages as $package) {
            $service->packages()->create($package);
        }
    }
}
