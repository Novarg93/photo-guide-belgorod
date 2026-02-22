<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Example;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = fake()->optional()->randomElement([
            null,
            'photos/placeholder.svg',
            'photos/example-cover.jpg',
        ]);

        $title = $path !== null
            ? pathinfo($path, PATHINFO_FILENAME)
            : fake()->sentence(3);

        return [
            'title' => $title,
            'category_id' => Category::factory(),
            'filter_option_keys' => [],
            'example_id' => Example::factory(),
            'path' => $path,
            'source_type' => fake()->randomElement(['own', 'permission', 'stock']),
            'source_url' => fake()->optional()->url(),
            'author_name' => fake()->optional()->name(),
            'license' => fake()->randomElement(['own', 'permission', 'stock', 'stock-commercial']),
            'permission_note' => fake()->optional()->sentence(10),
            'is_active' => false,
        ];
    }
}
