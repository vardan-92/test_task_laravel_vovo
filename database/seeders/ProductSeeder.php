<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->orderBy('id')->get();

        if ($categories->isEmpty()) {
            return;
        }

        $rows = [
            ['name' => 'MacBook Air 13', 'price' => 129990, 'category' => 'Ноутбуки', 'in_stock' => true, 'rating' => 4.8],
            ['name' => 'Lenovo ThinkPad X1', 'price' => 98900, 'category' => 'Ноутбуки', 'in_stock' => true, 'rating' => 4.6],
            ['name' => 'ASUS Vivobook 15', 'price' => 54990, 'category' => 'Ноутбуки', 'in_stock' => false, 'rating' => 4.1],
            ['name' => 'HP Pavilion 14', 'price' => 47990, 'category' => 'Ноутбуки', 'in_stock' => true, 'rating' => 3.9],
            ['name' => 'Dell XPS 13', 'price' => 112000, 'category' => 'Ноутбуки', 'in_stock' => true, 'rating' => 4.7],

            ['name' => 'iPhone 15 Pro', 'price' => 119990, 'category' => 'Смартфоны', 'in_stock' => true, 'rating' => 4.9],
            ['name' => 'Samsung Galaxy S24', 'price' => 89990, 'category' => 'Смартфоны', 'in_stock' => true, 'rating' => 4.5],
            ['name' => 'Google Pixel 8', 'price' => 64990, 'category' => 'Смартфоны', 'in_stock' => false, 'rating' => 4.4],
            ['name' => 'Xiaomi 14', 'price' => 59990, 'category' => 'Смартфоны', 'in_stock' => true, 'rating' => 4.3],
            ['name' => 'Nothing Phone (2)', 'price' => 42990, 'category' => 'Смартфоны', 'in_stock' => true, 'rating' => 4.0],

            ['name' => 'Sony WH-1000XM5', 'price' => 29990, 'category' => 'Аудио', 'in_stock' => true, 'rating' => 4.8],
            ['name' => 'AirPods Pro 2', 'price' => 24990, 'category' => 'Аудио', 'in_stock' => true, 'rating' => 4.7],
            ['name' => 'JBL Flip 6', 'price' => 8990, 'category' => 'Аудио', 'in_stock' => true, 'rating' => 4.2],
            ['name' => 'Sennheiser HD 560S', 'price' => 17990, 'category' => 'Аудио', 'in_stock' => false, 'rating' => 4.6],

            ['name' => 'Logitech MX Master 3S', 'price' => 8990, 'category' => 'Периферия', 'in_stock' => true, 'rating' => 4.7],
            ['name' => 'Keychron K8', 'price' => 7490, 'category' => 'Периферия', 'in_stock' => true, 'rating' => 4.4],
            ['name' => 'Razer DeathAdder V3', 'price' => 6990, 'category' => 'Периферия', 'in_stock' => true, 'rating' => 4.5],
            ['name' => 'Dell UltraSharp 27', 'price' => 35990, 'category' => 'Периферия', 'in_stock' => false, 'rating' => 4.6],

            ['name' => 'Samsung 990 PRO 1TB', 'price' => 10990, 'category' => 'Комплектующие', 'in_stock' => true, 'rating' => 4.9],
            ['name' => 'Corsair Vengeance 32GB', 'price' => 8990, 'category' => 'Комплектующие', 'in_stock' => true, 'rating' => 4.5],
            ['name' => 'AMD Ryzen 7 7800X3D', 'price' => 32990, 'category' => 'Комплектующие', 'in_stock' => true, 'rating' => 4.8],
            ['name' => 'NVIDIA RTX 4070', 'price' => 62990, 'category' => 'Комплектующие', 'in_stock' => false, 'rating' => 4.7],
            ['name' => 'be quiet! Straight Power 12', 'price' => 13990, 'category' => 'Комплектующие', 'in_stock' => true, 'rating' => 4.6],
        ];

        foreach ($rows as $row) {
            $category = $categories->firstWhere('name', $row['category']);
            if (! $category) {
                continue;
            }

            Product::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'price' => $row['price'],
                    'category_id' => $category->id,
                    'in_stock' => $row['in_stock'],
                    'rating' => $row['rating'],
                ]
            );
        }

        // Дополнительные товары для пагинации (однотипные названия с суффиксом)
        $base = $categories->firstWhere('name', 'Периферия') ?? $categories->first();
        for ($i = 1; $i <= 30; $i++) {
            Product::query()->updateOrCreate(
                ['name' => "USB-C cable {$i}m"],
                [
                    'price' => 290 + ($i * 37) % 700,
                    'category_id' => $base->id,
                    'in_stock' => $i % 4 !== 0,
                    'rating' => round(3 + ($i % 25) / 10, 2),
                ]
            );
        }
    }
}
