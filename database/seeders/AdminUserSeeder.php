<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
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

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'id' => Str::uuid()->toString(),
                'phone_number' => '0123456789',
                'email' => 'admin@example.com',
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'gender' => true,
                'birthday' => now(),
                'role' => true,
                'is_admin' => true,
                'is_active' => true,
                'avatar_id' => $defaultImage->id,
                'cover_image_id' => $defaultImage->id,
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
    }
}
