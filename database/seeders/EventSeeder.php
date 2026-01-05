<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();

        if ($venues->isEmpty()) {
            $this->command->warn('Please run VenueSeeder first!');
            return;
        }

        // Map events to venue names
        $eventVenueMap = [
            'Football Championship 2024' => 'Football Center Hoang Mai',
            'Badminton Tournament' => 'Badminton Club Cau Giay',
            'Tennis Open Day' => 'Tennis Academy',
            'Basketball League Finals' => 'Multi-Sport Complex',
            'Volleyball Youth Tournament' => 'Volleyball Arena',
        ];

        $events = [
            [
                'name' => 'Football Championship 2024',
                'price' => 500000,
                'ticket_number' => 100,
                'level' => 'Professional',
                'start_date' => now()->addDays(30)->setTime(18, 0, 0),
                'end_date' => now()->addDays(30)->setTime(22, 0, 0),
            ],
            [
                'name' => 'Badminton Tournament',
                'price' => 300000,
                'ticket_number' => 50,
                'level' => 'Intermediate',
                'start_date' => now()->addDays(45)->setTime(9, 0, 0),
                'end_date' => now()->addDays(45)->setTime(17, 0, 0),
            ],
            [
                'name' => 'Tennis Open Day',
                'price' => 200000,
                'ticket_number' => 30,
                'level' => 'Beginner to Advanced',
                'start_date' => now()->addDays(20)->setTime(8, 0, 0),
                'end_date' => now()->addDays(20)->setTime(20, 0, 0),
            ],
            [
                'name' => 'Basketball League Finals',
                'price' => 400000,
                'ticket_number' => 80,
                'level' => 'Professional',
                'start_date' => now()->addDays(60)->setTime(19, 0, 0),
                'end_date' => now()->addDays(60)->setTime(23, 0, 0),
            ],
            [
                'name' => 'Volleyball Youth Tournament',
                'price' => 150000,
                'ticket_number' => 40,
                'level' => 'Youth',
                'start_date' => now()->addDays(15)->setTime(14, 0, 0),
                'end_date' => now()->addDays(15)->setTime(18, 0, 0),
            ],
        ];

        foreach ($events as $eventData) {
            $venueName = $eventVenueMap[$eventData['name']] ?? null;
            $venue = $venueName ? $venues->firstWhere('name', $venueName) : $venues->random();

            if (!$venue) {
                $this->command->warn("Venue '{$venueName}' not found for event '{$eventData['name']}'. Skipping...");
                continue;
            }

            Event::firstOrCreate(
                ['name' => $eventData['name']],
                array_merge($eventData, [
                    'id' => Str::uuid()->toString(),
                    'venue_id' => $venue->id,
                ])
            );
        }

        $this->command->info('Events seeded successfully!');
    }
}
