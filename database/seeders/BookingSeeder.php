<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Ground;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->get(); // Lấy user thường
        $grounds = Ground::all();
        $statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

        if ($grounds->isEmpty() || $users->isEmpty()) return;

        foreach ($grounds as $ground) {
            // Mỗi sân tạo 8 booking
            for ($i = 0; $i < 8; $i++) {
                $startTime = rand(6, 20);
                $duration = rand(1, 2);
                $pricePerHour = 200000;
                
                // Random ngày trong khoảng 3 ngày kể từ hiện tại (VD: Hôm nay, mai, kia)
                $bookingDate = Carbon::now()->addDays(rand(0, 3))->format('Y-m-d');

                Booking::create([
                    'id' => Str::uuid()->toString(),
                    'user_id' => $users->random()->id,
                    'ground_id' => $ground->id,
                    'date' => $bookingDate,
                    'start_time' => sprintf('%02d:00:00', $startTime),
                    'end_time' => sprintf('%02d:00:00', $startTime + $duration),
                    'amount_time' => $duration,
                    'is_event' => false,
                    'quantity' => 1,
                    'total_price' => $duration * $pricePerHour,
                    'status' => $statuses[array_rand($statuses)],
                    'target' => rand(0,1) ? 'Adult' : 'Student',
                ]);
            }
        }
        $this->command->info('8 Bookings seeded per Ground (within 3 days)!');
    }
}