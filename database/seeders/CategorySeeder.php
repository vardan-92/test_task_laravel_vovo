<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Ноутбуки',
            'Смартфоны',
            'Аудио',
            'Периферия',
            'Комплектующие',
        ];

        foreach ($names as $name) {
            Category::query()->firstOrCreate(['name' => $name]);
        }
    }
}
