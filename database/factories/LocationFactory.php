<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->streetName();

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(18),
            'seo_title' => "{$name} - photo sessions in Belgorod",
            'seo_description' => fake()->sentence(20),
            'photo_path' => 'photos/placeholder.svg',
            'example_photo_paths' => [
                'photos/placeholder.svg',
                'photos/placeholder.svg',
            ],
            'filter_option_keys' => [],
            'is_active' => true,
        ];
    }
}
