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
            $amountTime = $endHour - $startHour;
            $totalPrice = $amountTime * rand(100000, 200000); // Giả sử giá mỗi giờ

            Booking::create([
                'id' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'date' => $date,
                'start_time' => sprintf('%02d:00:00', $startHour),
                'end_time' => sprintf('%02d:00:00', $endHour),
                'amount_time' => $amountTime,
                'is_event' => false,
                'ground_id' => $ground->id,
                'target' => rand(0, 1) ? 'student' : 'adult',
                'customer_note' => rand(0, 1) ? 'Please prepare the equipment' : null,
                'quantity' => rand(10, 30),
                'total_price' => $totalPrice,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        // Create event bookings
        if ($events->isNotEmpty()) {
            for ($i = 0; $i < 10; $i++) {
                $user = $users->random();
                $event = $events->random();
                $quantity = rand(1, 5);
                $totalPrice = $event->price * $quantity; // Tổng giá = giá vé * số lượng vé

                Booking::create([
                    'id' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'date' => null, // Event booking không cần date
                    'start_time' => null, // Event booking không cần start_time
                    'end_time' => null, // Event booking không cần end_time
                    'amount_time' => null, // Event booking không cần amount_time
                    'is_event' => true,
                    'ground_id' => null, // Event booking không cần ground_id
                    'event_id' => $event->id,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                    'target' => null,
                    'customer_note' => rand(0, 1) ? 'Looking forward to the event!' : null,
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }

        $this->command->info('Bookings seeded successfully!');
    }
}
