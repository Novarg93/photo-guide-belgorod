<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Example>
 */
class ExampleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(14),
            'filter_option_keys' => [],
            'mood' => fake()->randomElement(['Romantic', 'Natural', 'Editorial', 'Warm']),
            'location_hint' => fake()->randomElement(['City center', 'Pine park', 'Studio loft', 'Riverside']),
            'season_hint' => fake()->randomElement(['Spring', 'Summer', 'Autumn', 'Winter']),
            'clothing_hint' => fake()->randomElement(['Casual pastel', 'Classic neutral', 'Elegant black', 'Denim and white']),
            'cover_image' => null,
            'is_active' => true,
        ];
    }
}
