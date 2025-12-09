<?php

namespace Database\Seeders;

use App\Models\Price;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();
        $categories = \App\Models\Category::all();

        if ($venues->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please run VenueSeeder and CategorySeeder first!');
            return;
        }

        $venueCategories = [
            'Football Center Hoang Mai' => ['Football'],
            'Badminton Club Cau Giay' => ['Badminton'],
            'Multi-Sport Complex' => ['Football', 'Basketball', 'Tennis'],
            'Tennis Academy' => ['Tennis', 'Table Tennis'],
            'Volleyball Arena' => ['Volleyball'],
        ];

        $timeSlots = [
            ['day' => 'Mon-Thu', 'start_time' => '06:00', 'end_time' => '12:00', 'current_price' => 100000, 'fixed_price' => 80000],
            ['day' => 'Mon-Thu', 'start_time' => '12:00', 'end_time' => '18:00', 'current_price' => 150000, 'fixed_price' => 120000],
            ['day' => 'Mon-Thu', 'start_time' => '18:00', 'end_time' => '22:00', 'current_price' => 200000, 'fixed_price' => 160000],
            ['day' => 'Fri-Sun', 'start_time' => '06:00', 'end_time' => '12:00', 'current_price' => 120000, 'fixed_price' => 100000],
            ['day' => 'Fri-Sun', 'start_time' => '12:00', 'end_time' => '18:00', 'current_price' => 180000, 'fixed_price' => 150000],
            ['day' => 'Fri-Sun', 'start_time' => '18:00', 'end_time' => '22:00', 'current_price' => 250000, 'fixed_price' => 200000],
        ];

        foreach ($venues as $venue) {
            $venueCategoryNames = $venueCategories[$venue->name] ?? [];
            
            if (empty($venueCategoryNames)) {
                continue;
            }

            $venueCategoriesList = $categories->filter(function($category) use ($venueCategoryNames) {
                return in_array($category->name, $venueCategoryNames);
            });
            
            if ($venueCategoriesList->isEmpty()) {
                continue;
            }

            foreach ($venueCategoriesList as $category) {
                foreach ($timeSlots as $slot) {
                    $price = Price::firstOrCreate(
                        [
                            'day' => $slot['day'],
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ],
                        array_merge($slot, [
                            'id' => Str::uuid()->toString(),
                        ])
                    );

                    DB::table('venues_categories')->updateOrInsert(
                        [
                            'venue_id' => $venue->id,
                            'category_id' => $category->id,
                        ],
                        [
                            'price_id' => $price->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        $this->command->info('Prices seeded successfully!');
    }
}
