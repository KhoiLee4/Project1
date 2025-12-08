<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::whereIn('status', ['Confirmed', 'Completed'])->get();

        if ($bookings->isEmpty()) {
            $this->command->warn('Please run BookingSeeder first!');
            return;
        }

        $methods = ['Cash', 'Card', 'Online'];
        $statuses = ['Pending', 'Paid', 'Refunded'];

        foreach ($bookings as $booking) {
            // Create 1-2 payments per booking
            $paymentCount = rand(1, 2);
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $amount = rand(100000, 1000000);
                $unitPrice = $amount / $booking->amount_time;

                Payment::create([
                    'id' => Str::uuid()->toString(),
                    'booking_id' => $booking->id,
                    'amount' => $amount,
                    'unit_price' => $unitPrice,
                    'method' => $methods[array_rand($methods)],
                    'note' => rand(0, 1) ? 'Payment for booking' : null,
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }

        $this->command->info('Payments seeded successfully!');
    }
}
