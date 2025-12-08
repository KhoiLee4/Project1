<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'name' => 'Football Championship 2024',
                'price' => 500000,
                'ticket_number' => 100,
                'level' => 'Professional',
            ],
            [
                'name' => 'Badminton Tournament',
                'price' => 300000,
                'ticket_number' => 50,
                'level' => 'Intermediate',
            ],
            [
                'name' => 'Tennis Open Day',
                'price' => 200000,
                'ticket_number' => 30,
                'level' => 'Beginner to Advanced',
            ],
            [
                'name' => 'Basketball League Finals',
                'price' => 400000,
                'ticket_number' => 80,
                'level' => 'Professional',
            ],
            [
                'name' => 'Volleyball Youth Tournament',
                'price' => 150000,
                'ticket_number' => 40,
                'level' => 'Youth',
            ],
        ];

        foreach ($events as $eventData) {
            Event::firstOrCreate(
                ['name' => $eventData['name']],
                array_merge($eventData, [
                    'id' => Str::uuid()->toString(),
                ])
            );
        }

        $this->command->info('Events seeded successfully!');
    }
}
