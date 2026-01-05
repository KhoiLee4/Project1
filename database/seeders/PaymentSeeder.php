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
                // Sử dụng total_price từ booking nếu có, nếu không thì tính toán
                if ($booking->total_price && $booking->total_price > 0) {
                    $amount = $booking->total_price;
                } else {
                    // Fallback: tính theo amount_time cho booking thường
                    if ($booking->amount_time && $booking->amount_time > 0) {
                        $amount = rand(100000, 1000000);
                    } else {
                        // Event booking: sử dụng giá mặc định
                        $amount = rand(150000, 500000);
                    }
                }
                
                // Tính unit_price: cho booking thường dựa trên amount_time, cho event dựa trên quantity
                if ($booking->is_event) {
                    $unitPrice = $booking->quantity > 0 ? $amount / $booking->quantity : $amount;
                } else {
                    $unitPrice = $booking->amount_time && $booking->amount_time > 0 
                        ? $amount / $booking->amount_time 
                        : $amount;
                }

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
