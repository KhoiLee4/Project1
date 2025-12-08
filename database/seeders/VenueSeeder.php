<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('is_admin', false)->get();
        $categories = Category::all();

        if ($owners->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please run UserSeeder and CategorySeeder first!');
            return;
        }

        // Create images for venues
        $venueImages = [];
        for ($i = 1; $i <= 10; $i++) {
            $venueImages[] = Image::firstOrCreate(
                ['id' => Str::uuid()->toString()],
                [
                    'name' => "Venue Image {$i}",
                    'image_url' => "https://via.placeholder.com/800x600?text=Venue+{$i}",
                ]
            );
        }

        $venues = [
            [
                'name' => 'Football Center Hoang Mai',
                'sub_address' => '123 Main Street',
                'district' => 'Hoang Mai',
                'city' => 'Hanoi',
                'address' => '123 Main Street, Hoang Mai District, Hanoi',
                'operating_time' => '6:00 - 22:00',
                'phone_number1' => '0241234567',
                'phone_number2' => '0241234568',
                'website' => 'https://football-center.example.com',
                'deposit' => 20.00,
                'categories' => ['Football'],
            ],
            [
                'name' => 'Badminton Club Cau Giay',
                'sub_address' => '456 Sports Avenue',
                'district' => 'Cau Giay',
                'city' => 'Hanoi',
                'address' => '456 Sports Avenue, Cau Giay District, Hanoi',
                'operating_time' => '5:00 - 23:00',
                'phone_number1' => '0242345678',
                'phone_number2' => null,
                'website' => null,
                'deposit' => 15.00,
                'categories' => ['Badminton'],
            ],
            [
                'name' => 'Multi-Sport Complex',
                'sub_address' => '789 Fitness Road',
                'district' => 'Ba Dinh',
                'city' => 'Hanoi',
                'address' => '789 Fitness Road, Ba Dinh District, Hanoi',
                'operating_time' => '6:00 - 22:00',
                'phone_number1' => '0243456789',
                'phone_number2' => '0243456790',
                'website' => 'https://multisport.example.com',
                'deposit' => 25.00,
                'categories' => ['Football', 'Basketball', 'Tennis'],
            ],
            [
                'name' => 'Tennis Academy',
                'sub_address' => '321 Court Street',
                'district' => 'Hai Ba Trung',
                'city' => 'Hanoi',
                'address' => '321 Court Street, Hai Ba Trung District, Hanoi',
                'operating_time' => '7:00 - 21:00',
                'phone_number1' => '0244567890',
                'phone_number2' => null,
                'website' => null,
                'deposit' => 30.00,
                'categories' => ['Tennis', 'Table Tennis'],
            ],
            [
                'name' => 'Volleyball Arena',
                'sub_address' => '654 Net Boulevard',
                'district' => 'Dong Da',
                'city' => 'Hanoi',
                'address' => '654 Net Boulevard, Dong Da District, Hanoi',
                'operating_time' => '6:00 - 22:00',
                'phone_number1' => '0245678901',
                'phone_number2' => '0245678902',
                'website' => null,
                'deposit' => 18.00,
                'categories' => ['Volleyball'],
            ],
        ];

        $imageIndex = 0;
        foreach ($venues as $index => $venueData) {
            $owner = $owners->random();
            $categoriesToAttach = $venueData['categories'];
            unset($venueData['categories']);

            $venue = Venue::firstOrCreate(
                ['name' => $venueData['name']],
                array_merge($venueData, [
                    'id' => Str::uuid()->toString(),
                    'owner_id' => $owner->id,
                ])
            );

            // Attach categories
            $categoryIds = Category::whereIn('name', $categoriesToAttach)->pluck('id');
            $venue->categories()->sync($categoryIds);

            // Attach images
            $imagesToAttach = [];
            for ($i = 0; $i < 3; $i++) {
                if ($imageIndex >= count($venueImages)) {
                    $imageIndex = 0;
                }
                $imagesToAttach[$venueImages[$imageIndex]->id] = ['is_image' => $i < 2];
                $imageIndex++;
            }
            $venue->images()->sync($imagesToAttach);
        }

        $this->command->info('Venues seeded successfully!');
    }
}
