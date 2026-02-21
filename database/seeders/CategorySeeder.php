<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Свадебная',
            'Семейная',
            'Love Story',
            'Индивидуальная',
            'Контент для соцсетей',
            'Беременность',
            'Детская',
        ];

        foreach ($categories as $name) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Категория фотосессий: {$name}.",
                    'is_active' => true,
                ],
            );
        }
    }
}
