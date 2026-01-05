<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy 7 owners (đã tạo ở bước trên dựa vào email)
        $owners = User::where('email', 'like', 'owner%')->get();
        // Lấy 5 ảnh mẫu
        $sampleImages = Image::where('name', 'like', 'Sample Image%')->get();

        if ($owners->isEmpty()) {
            $this->command->warn('Please run UserSeeder first!');
            return;
        }

        for ($i = 1; $i <= 15; $i++) {
            $venue = Venue::create([
                'id' => Str::uuid()->toString(),
                'name' => "Sports Center " . $i, // Tên đơn giản hoặc dùng Faker
                'sub_address' => "District " . rand(1, 12),
                'district' => "District " . rand(1, 12),
                'city' => "Ho Chi Minh City",
                'address' => rand(1, 999) . " Street Name, Ward " . rand(1, 10),
                'operating_time' => '05:00 - 23:00',
                'phone_number1' => '028' . rand(10000000, 99999999),
                'deposit' => rand(1, 5) * 50000, // 50k - 250k
                'owner_id' => $owners->random()->id,
            ]);

            // Gắn 1 ảnh ngẫu nhiên cho Venue vào bảng trung gian image_venus
            if ($sampleImages->isNotEmpty()) {
                DB::table('image_venus')->insert([
                    'venue_id' => $venue->id,
                    'image_id' => $sampleImages->random()->id,
                    'is_image' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->command->info('15 Venues created with images assigned!');
    }
}