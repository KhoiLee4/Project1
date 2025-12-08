<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Football',
            'Badminton',
            'Tennis',
            'Basketball',
            'Volleyball',
            'Table Tennis',
            'Pickleball',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['name' => $categoryName],
                ['id' => Str::uuid()->toString()]
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
