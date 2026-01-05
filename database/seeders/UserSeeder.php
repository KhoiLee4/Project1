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
        $defaultImage = Image::where('name', 'Default Avatar')->first();
        $avatarId = $defaultImage ? $defaultImage->id : null;

        // 1. Tạo 1 Admin (Nếu AdminUserSeeder chạy riêng thì bỏ qua, nhưng để đây cho chắc)
        User::firstOrCreate(
            ['email' => 'admin@system.com'],
            [
                'id' => Str::uuid()->toString(),
                'phone_number' => '0900000000',
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'role' => 0, // 0: Admin
                'is_active' => true,
                'avatar_id' => $avatarId
            ]
        );

        // 2. Tạo 7 Owners
        for ($i = 1; $i <= 7; $i++) {
            User::create([
                'id' => Str::uuid()->toString(),
                'phone_number' => '091000000' . $i,
                'email' => "owner{$i}@example.com",
                'name' => "Venue Owner {$i}",
                'password' => Hash::make('password'),
                'is_admin' => false,
                'role' => 0, // Giả sử Owner cũng có role đặc biệt hoặc giống Admin cấp thấp
                'is_active' => true,
                'avatar_id' => $avatarId
            ]);
        }

        // 3. Tạo 15 Regular Users
        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'id' => Str::uuid()->toString(),
                'phone_number' => '09200000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'email' => "user{$i}@example.com",
                'name' => "Regular User {$i}",
                'password' => Hash::make('password'),
                'is_admin' => false,
                'role' => 1, // 1: User thường
                'is_active' => true,
                'avatar_id' => $avatarId
            ]);
        }
        
        $this->command->info('Users (1 Admin, 7 Owners, 15 Users) seeded!');
    }
}