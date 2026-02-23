<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegalPage>
 */
class LegalPageFactory extends Factory
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
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(16),
            'content' => implode("\n\n", fake()->paragraphs(5)),
            'seo_title' => "{$title} - Photo Guide Belgorod",
            'seo_description' => fake()->sentence(20),
            'sort_order' => fake()->numberBetween(1, 200),
            'is_active' => true,
        ];
    }
}
