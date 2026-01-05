<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ImageSeeder::class,      // Chạy đầu tiên để có ảnh
            CategorySeeder::class,   // Master data
            UserSeeder::class,       // Tạo Admin, Owner, User
            VenueSeeder::class,      // Tạo Venue, gắn Owner & Ảnh
            PriceListSeeder::class,  // Gắn Category cho Venue
            GroundSeeder::class,     // Tạo Ground dựa trên Category
            ServiceListSeeder::class,// (Dùng file cũ của bạn, nó sẽ tự lặp qua Venue::all)
            TermSeeder::class,       // (Dùng file cũ của bạn)
            EventSeeder::class,      // 2 Event/Venue
            BookingSeeder::class,    // 8 Booking/Ground
            PaymentSeeder::class,    // (Dùng file cũ, nó sẽ lấy Booking để tạo Payment)
            RatingSeeder::class,     // (Dùng file cũ, nó sẽ lặp qua Venue::all)
        ]);
    }
}