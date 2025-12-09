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
        $users = User::where('is_admin', false)->get();
        $venues = Venue::all();

        if ($users->isEmpty() || $venues->isEmpty()) {
            $this->command->warn('Please run UserSeeder and VenueSeeder first!');
            return;
        }

        $reviews = [
            'Great venue with excellent facilities!',
            'Very clean and well-maintained.',
            'Good value for money.',
            'The staff is very friendly and helpful.',
            'Perfect location, easy to access.',
            'Could be better, but overall okay.',
            'Amazing experience! Will come back.',
            'The equipment is in good condition.',
            'Nice atmosphere and good service.',
            'Highly recommended!',
        ];

        // Create ratings for each venue
        foreach ($venues as $venue) {
            $ratingCount = rand(3, 8);
            $ratedUsers = $users->random(min($ratingCount, $users->count()));

            foreach ($ratedUsers as $user) {
                Rating::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'venue_id' => $venue->id,
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'star_number' => rand(3, 5),
                        'review' => $reviews[array_rand($reviews)],
                    ]
                );
            }
        }

        $this->command->info('Ratings seeded successfully!');
    }
}
