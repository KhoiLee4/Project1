<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultImage = Image::firstOrCreate(
            ['id' => Str::uuid()->toString()],
            [
                'name' => 'Default Image',
                'image_url' => 'https://via.placeholder.com/300',
            ]
        );

        // Create owner users
        $owners = [
            [
                'phone_number' => '0987654321',
                'email' => 'owner1@example.com',
                'name' => 'John Owner',
                'password' => Hash::make('password'),
                'gender' => false,
                'birthday' => now()->subYears(35),
                'role' => true,
                'is_admin' => false,
                'is_active' => true,
            ],
            [
                'phone_number' => '0987654322',
                'email' => 'owner2@example.com',
                'name' => 'Jane Owner',
                'password' => Hash::make('password'),
                'gender' => true,
                'birthday' => now()->subYears(30),
                'role' => true,
                'is_admin' => false,
                'is_active' => true,
            ],
        ];

        foreach ($owners as $ownerData) {
            User::firstOrCreate(
                ['email' => $ownerData['email']],
                array_merge($ownerData, [
                    'id' => Str::uuid()->toString(),
                    'avatar_id' => $defaultImage->id,
                    'cover_image_id' => $defaultImage->id,
                ])
            );
        }

        // Create regular users
        $users = [
            [
                'phone_number' => '0123456780',
                'email' => 'user1@example.com',
                'name' => 'Alice User',
                'password' => Hash::make('password'),
                'gender' => true,
                'birthday' => now()->subYears(25),
            ],
            [
                'phone_number' => '0123456781',
                'email' => 'user2@example.com',
                'name' => 'Bob User',
                'password' => Hash::make('password'),
                'gender' => false,
                'birthday' => now()->subYears(28),
            ],
            [
                'phone_number' => '0123456782',
                'email' => 'user3@example.com',
                'name' => 'Charlie User',
                'password' => Hash::make('password'),
                'gender' => false,
                'birthday' => now()->subYears(22),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'id' => Str::uuid()->toString(),
                    'role' => true,
                    'is_admin' => false,
                    'is_active' => true,
                    'avatar_id' => $defaultImage->id,
                    'cover_image_id' => $defaultImage->id,
                ])
            );
        }

        $this->command->info('Users seeded successfully!');
    }
}
