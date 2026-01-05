<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả user trừ admin (bao gồm cả Owner và User thường) để có nhiều người đánh giá
        $users = User::where('is_admin', false)->get();
        $venues = Venue::all();

        if ($users->isEmpty() || $venues->isEmpty()) {
            $this->command->warn('Please run UserSeeder and VenueSeeder first!');
            return;
        }

        // Danh sách review phong phú hơn
        $reviews = [
            // Tiếng Anh
            'Great venue with excellent facilities!',
            'Very clean and well-maintained. The restrooms are spotless.',
            'Good value for money. Will definitely book again.',
            'The staff is very friendly and helpful.',
            'Perfect location, easy to access from the main road.',
            'Could be better, lighting was a bit dim in the evening.',
            'Amazing experience! The turf quality is top-notch.',
            'The equipment is in good condition, but the price is slightly high.',
            'Nice atmosphere and good service.',
            'Highly recommended for weekend matches!',
            'Parking space is a bit limited, but the court is great.',
            'Best sports center in the district!',
            
            // Tiếng Việt (Thêm vào cho tự nhiên nếu muốn)
            'Sân đẹp, cỏ nhân tạo mới, đá rất êm chân.',
            'Nhân viên nhiệt tình, có chỗ để xe rộng rãi.',
            'Giá cả hợp lý so với mặt bằng chung.',
            'Đèn sân sáng, đá buổi tối rất ok.',
            'Sẽ quay lại ủng hộ dài dài.',
            'Dịch vụ nước uống đầy đủ, tiện lợi.',
        ];

        foreach ($venues as $venue) {
            // Tăng số lượng đánh giá: Mỗi sân sẽ được 50% - 90% số user vào đánh giá
            // Với 22 users, mỗi sân sẽ có khoảng 11 - 20 đánh giá
            $percentage = rand(50, 90) / 100;
            $ratingCount = intval($users->count() * $percentage);
            
            // Đảm bảo ít nhất 5 đánh giá
            $ratingCount = max($ratingCount, 5);
            
            // Lấy ngẫu nhiên danh sách user sẽ đánh giá sân này
            $ratedUsers = $users->random(min($ratingCount, $users->count()));

            foreach ($ratedUsers as $user) {
                // Random số sao, tỉ lệ 5 sao cao hơn
                $star = rand(1, 100) <= 10 ? rand(1, 2) : rand(3, 5); 

                Rating::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'venue_id' => $venue->id,
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'star_number' => $star,
                        'review' => $reviews[array_rand($reviews)],
                        'created_at' => now()->subDays(rand(0, 60)), // Rải rác trong 2 tháng qua
                    ]
                );
            }
        }

        $this->command->info('Ratings seeded successfully! Each venue now has plenty of reviews.');
    }
}