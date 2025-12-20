<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // <--- WAJIB ADA

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Perangkat elektronik seperti smartphone, laptop, dan gadget lainnya',
                'is_active' => true,
            ],
            [
                'name' => 'Fashion Pria',
                'slug' => 'fashion-pria',
                'description' => 'Pakaian, sepatu, dan aksesoris untuk pria',
                'is_active' => true,
            ],
            [
                'name' => 'Fashion Wanita',
                'slug' => 'fashion-wanita',
                'description' => 'Pakaian, sepatu, dan aksesoris untuk wanita',
                'is_active' => true,
            ],
            [
                'name' => 'Makanan & Minuman',
                'slug' => 'makanan-minuman',
                'description' => 'Berbagai makanan ringan, minuman, dan bahan makanan',
                'is_active' => true,
            ],
            [
                'name' => 'Kesehatan & Kecantikan',
                'slug' => 'kesehatan-kecantikan',
                'description' => 'Produk kesehatan, skincare, dan kosmetik',
                'is_active' => true,
            ],
            [
                'name' => 'Rumah Tangga',
                'slug' => 'rumah-tangga',
                'description' => 'Peralatan rumah tangga dan dekorasi',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            // Menggunakan updateOrCreate agar tidak duplikat jika seeder dijalankan ulang
            Category::updateOrCreate(
                ['slug' => $category['slug']], 
                $category
            );
        }

        $this->command->info('✅ Categories seeded successfully!');
    }
}