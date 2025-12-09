<?php

namespace Database\Seeders;

use App\Models\Term;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TermSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();

        if ($venues->isEmpty()) {
            $this->command->warn('Please run VenueSeeder first!');
            return;
        }

        $termCategories = [
            1 => 'Booking Terms',
            2 => 'Cancellation Policy',
            3 => 'Payment Terms',
            4 => 'General Rules',
        ];

        $termContents = [
            1 => 'Bookings must be made at least 24 hours in advance. Full payment is required upon confirmation.',
            2 => 'Cancellations made 48 hours before the booking time will receive a full refund. Cancellations made 24-48 hours before will receive a 50% refund. No refund for cancellations made less than 24 hours before.',
            3 => 'Payment can be made via cash, card, or online transfer. A deposit may be required for certain bookings.',
            4 => 'Please respect the facilities and other users. Equipment must be returned in good condition. Smoking and alcohol are not permitted.',
        ];

        foreach ($venues as $venue) {
            foreach ($termCategories as $categoryId => $categoryName) {
                Term::firstOrCreate(
                    [
                        'venue_id' => $venue->id,
                        'term_category' => $categoryId,
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'content' => $termContents[$categoryId],
                        'update_time' => now(),
                    ]
                );
            }
        }

        $this->command->info('Terms seeded successfully!');
    }
}

