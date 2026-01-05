<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Price;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ bảng trung gian để tránh trùng lặp nếu chạy lại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('venues_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $venues = Venue::all();
        $categories = Category::all();

        if ($venues->isEmpty() || $categories->isEmpty()) return;

        $timeSlots = [
            ['day' => 'Mon-Fri', 'start_time' => '05:00', 'end_time' => '17:00', 'current_price' => 100000],
            ['day' => 'Mon-Fri', 'start_time' => '17:00', 'end_time' => '23:00', 'current_price' => 150000],
            ['day' => 'Sat-Sun', 'start_time' => '05:00', 'end_time' => '23:00', 'current_price' => 200000],
        ];

        foreach ($venues as $venue) {
            // Mỗi venue lấy NGẪU NHIÊN 2 category khác nhau
            $venueCategories = $categories->random(2); 

            foreach ($venueCategories as $category) {
                // Tạo giá cho category này tại venue này
                $slot = $timeSlots[array_rand($timeSlots)];
                
                $price = Price::create([
                    'id' => Str::uuid()->toString(),
                    'day' => $slot['day'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'current_price' => $slot['current_price'],
                    'fixed_price' => $slot['current_price'] * 0.8,
                ]);

                // Insert vào bảng trung gian
                DB::table('venues_categories')->insert([
                    'venue_id' => $venue->id,
                    'category_id' => $category->id,
                    'price_id' => $price->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->command->info('Assigned 2 Categories to each of 15 Venues!');
    }
}