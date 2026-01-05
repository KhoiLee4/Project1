<?php

namespace Database\Seeders;

use App\Models\Ground;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroundSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả các cặp Venue-Category đã tạo ở PriceListSeeder
        $venueCategories = DB::table('venues_categories')->get();

        foreach ($venueCategories as $vc) {
            // Lấy tên category để đặt tên sân cho đẹp
            $catName = DB::table('categories')->where('id', $vc->category_id)->value('name');

            Ground::create([
                'id' => Str::uuid()->toString(),
                'venue_id' => $vc->venue_id,
                'category_id' => $vc->category_id,
                'name' => "Sân " . $catName . " Vip", // VD: Sân Football Vip
            ]);
        }
        $this->command->info('Grounds seeded based on Venue Categories!');
    }
}