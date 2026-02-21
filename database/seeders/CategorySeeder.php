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
            'Wedding',
            'Family',
            'Love Story',
            'Personal',
            'Social Content',
            'Pregnancy',
            'Kids',
        ];

        foreach ($categories as $name) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Photo session category: {$name}.",
                    'is_active' => true,
                ],
            );
        }
    }
}
