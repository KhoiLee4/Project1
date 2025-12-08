<?php

namespace Database\Seeders;

use App\Models\PriceList;
use App\Models\PriceListDetail;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::with('categories')->get();

        if ($venues->isEmpty()) {
            $this->command->warn('Please run VenueSeeder first!');
            return;
        }

        foreach ($venues as $venue) {
            $categories = $venue->categories;
            
            if ($categories->isEmpty()) {
                continue;
            }

            // Create a price list for each category
            foreach ($categories as $category) {
                $priceList = PriceList::firstOrCreate(
                    [
                        'venue_id' => $venue->id,
                        'name' => $category->name,
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                    ]
                );

                // Create price list details
                $timeSlots = [
                    ['day' => 'Mon-Thu', 'start_time' => '06:00', 'end_time' => '12:00', 'current_price' => 100000, 'fixed_price' => 80000],
                    ['day' => 'Mon-Thu', 'start_time' => '12:00', 'end_time' => '18:00', 'current_price' => 150000, 'fixed_price' => 120000],
                    ['day' => 'Mon-Thu', 'start_time' => '18:00', 'end_time' => '22:00', 'current_price' => 200000, 'fixed_price' => 160000],
                    ['day' => 'Fri-Sun', 'start_time' => '06:00', 'end_time' => '12:00', 'current_price' => 120000, 'fixed_price' => 100000],
                    ['day' => 'Fri-Sun', 'start_time' => '12:00', 'end_time' => '18:00', 'current_price' => 180000, 'fixed_price' => 150000],
                    ['day' => 'Fri-Sun', 'start_time' => '18:00', 'end_time' => '22:00', 'current_price' => 250000, 'fixed_price' => 200000],
                ];

                foreach ($timeSlots as $slot) {
                    PriceListDetail::firstOrCreate(
                        [
                            'price_list_id' => $priceList->id,
                            'day' => $slot['day'],
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ],
                        array_merge($slot, [
                            'id' => Str::uuid()->toString(),
                        ])
                    );
                }
            }
        }

        $this->command->info('Price lists seeded successfully!');
    }
}
