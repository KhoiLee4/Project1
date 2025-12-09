<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ground;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroundSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();
        $categories = Category::all();

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

        foreach ($venues as $venue) {
            $venueCategoryNames = $venueCategories[$venue->name] ?? [];
            
            if (empty($venueCategoryNames)) {
                continue;
            }

            $venueCategoriesList = Category::whereIn('name', $venueCategoryNames)->get();
            
            if ($venueCategoriesList->isEmpty()) {
                continue;
            }

            // Create 2-4 grounds per venue
            $groundCount = rand(2, 4);
            
            for ($i = 1; $i <= $groundCount; $i++) {
                $category = $venueCategoriesList->random();
                
                Ground::firstOrCreate(
                    [
                        'venue_id' => $venue->id,
                        'name' => "Ground {$i}",
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'category_id' => $category->id,
                    ]
                );
            }
        }

        $this->command->info('Grounds seeded successfully!');
    }
}
