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
        $eventNames = ['Summer Cup', 'Winter League', 'Friendly Match', 'Pro Tournament', 'Charity Gala'];

        foreach ($venues as $venue) {
            for ($i = 0; $i < 2; $i++) {
                Event::create([
                    'id' => Str::uuid()->toString(),
                    'venue_id' => $venue->id,
                    'name' => $eventNames[array_rand($eventNames)] . " @ " . $venue->name,
                    'price' => rand(1, 5) * 100000,
                    'ticket_number' => rand(50, 200),
                    'level' => 'Open',
                    'start_date' => now()->addDays(rand(5, 30)),
                    'end_date' => now()->addDays(rand(31, 60)),
                ]);
            }
        }
        $this->command->info('2 Events created per Venue!');
    }
}