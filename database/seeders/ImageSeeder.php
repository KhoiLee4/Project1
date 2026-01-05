<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        $urls = [
            'https://res.cloudinary.com/dhw9dvfw1/image/upload/v1767652455/images/01KE84QNW7PKP97M7EYVK4N524.png',
            'https://res.cloudinary.com/dhw9dvfw1/image/upload/v1767652487/images/01KE84RTR1XJ3269PPWWV6ZTMB.jpg',
            'https://res.cloudinary.com/dhw9dvfw1/image/upload/v1767652518/images/01KE84SQ1QHAP77X3FJMGPMD76.webp',
            'https://res.cloudinary.com/dhw9dvfw1/image/upload/v1767652592/images/01KE84VWYPT0MZF7J4WBZ9DJG9.jpg',
            'https://res.cloudinary.com/dhw9dvfw1/image/upload/v1767652620/images/01KE84WWHB8EB2VM9MFSXQXDER.webp',
        ];

        foreach ($urls as $index => $url) {
            Image::firstOrCreate(
                ['image_url' => $url],
                [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Sample Image ' . ($index + 1),
                ]
            );
        }
        
        // Tạo thêm một ảnh default avatar cho user nếu cần
        Image::firstOrCreate(
            ['name' => 'Default Avatar'],
            ['id' => Str::uuid()->toString(), 'image_url' => 'https://via.placeholder.com/150']
        );
    }
}