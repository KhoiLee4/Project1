<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            VenueSeeder::class,
            GroundSeeder::class,
            PriceListSeeder::class,
            ServiceListSeeder::class,
            TermSeeder::class,
            EventSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
            RatingSeeder::class,
        ]);
    }
}
