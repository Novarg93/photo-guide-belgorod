<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photographer>
 */
class PhotographerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'url' => fake()->optional()->url(),
            'image_path' => Arr::random([
                'photos/placeholder.svg',
                'locations/placeholder.svg',
            ]),
            'description' => fake()->sentence(20),
            'is_active' => true,
        ];
    }
}
