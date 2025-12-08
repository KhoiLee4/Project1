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

            // Create 2-4 grounds per venue
            $groundCount = rand(2, 4);
            
            for ($i = 1; $i <= $groundCount; $i++) {
                $category = $categories->random();
                
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
