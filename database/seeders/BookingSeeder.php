<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ground;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $grounds = Ground::all();
        $events = Event::all();

        if ($users->isEmpty() || $grounds->isEmpty()) {
            $this->command->warn('Please run UserSeeder, VenueSeeder, and GroundSeeder first!');
            return;
        }

        $statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
        
        // Create normal bookings
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $ground = $grounds->random();
            $date = now()->addDays(rand(1, 30));
            $startHour = rand(6, 18);
            $endHour = $startHour + rand(1, 3);

            Booking::create([
                'id' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'date' => $date,
                'start_time' => sprintf('%02d:00:00', $startHour),
                'end_time' => sprintf('%02d:00:00', $endHour),
                'amount_time' => $endHour - $startHour,
                'is_event' => false,
                'ground_id' => $ground->id,
                'target' => rand(0, 1) ? 'Students' : null,
                'customer_note' => rand(0, 1) ? 'Please prepare the equipment' : null,
                'quantity' => rand(10, 30),
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        // Create event bookings
        if ($events->isNotEmpty()) {
            for ($i = 0; $i < 5; $i++) {
                $user = $users->random();
                $ground = $grounds->random();
                $event = $events->random();
                $date = now()->addDays(rand(1, 30));

                Booking::create([
                    'id' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'date' => $date,
                    'start_time' => '18:00:00',
                    'end_time' => '22:00:00',
                    'amount_time' => 4,
                    'is_event' => true,
                    'ground_id' => $ground->id,
                    'event_id' => $event->id,
                    'quantity' => rand(1, 5),
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }

        $this->command->info('Bookings seeded successfully!');
    }
}
