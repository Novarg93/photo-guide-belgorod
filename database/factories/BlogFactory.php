<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'cover_image' => 'photos/placeholder.svg',
            'excerpt' => fake()->sentence(16),
            'content' => fake()->paragraphs(5, true),
            'seo_title' => "{$title} | Photo Guide Belgorod",
            'seo_description' => fake()->sentence(22),
            'published_at' => now()->subDays(fake()->numberBetween(1, 180)),
            'is_active' => true,
        ];
    }
}
