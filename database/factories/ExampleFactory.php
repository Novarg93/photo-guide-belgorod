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

    public function withMixedCategoryFilters(Category|int|null $category = null, int $offset = 0): static
    {
        return $this->state(function (array $attributes) use ($category, $offset): array {
            $resolvedCategory = $this->resolveCategory($category, $attributes['category_id'] ?? null);

            if (! $resolvedCategory instanceof Category) {
                return [
                    'filter_option_keys' => [],
                ];
            }

            return [
                'filter_option_keys' => $this->buildMixedFilterKeys(
                    $resolvedCategory,
                    $offset === 0 ? $resolvedCategory->id : $offset,
                ),
            ];
        });
    }

    private function resolveCategory(Category|int|null $category, mixed $attributeCategoryId): ?Category
    {
        if ($category instanceof Category) {
            return $category;
        }

        if (is_int($category)) {
            return Category::query()->find($category);
        }

        if ($attributeCategoryId instanceof Category) {
            return $attributeCategoryId;
        }

        if (is_int($attributeCategoryId)) {
            return Category::query()->find($attributeCategoryId);
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function buildMixedFilterKeys(Category $category, int $offset): array
    {
        $filterKeys = collect($category->filter_groups ?? [])
            ->flatMap(function (mixed $group): array {
                if (! is_array($group) || ! isset($group['name']) || ! is_array($group['options'] ?? null)) {
                    return [];
                }

                $groupKey = Str::slug((string) $group['name']);

                return collect($group['options'])
                    ->map(function (mixed $option) use ($groupKey): ?string {
                        $name = trim((string) (is_array($option) ? ($option['name'] ?? '') : ''));

                        if ($name === '') {
                            return null;
                        }

                        return "{$groupKey}.".Str::slug($name);
                    })
                    ->filter()
                    ->values()
                    ->all();
            })
            ->values()
            ->all();

        if ($filterKeys === []) {
            return [];
        }

        $desiredCount = min(count($filterKeys), min(4, 2 + ($offset % 3)));
        $normalizedOffset = $offset % count($filterKeys);
        $rotatedFilterKeys = array_values([
            ...array_slice($filterKeys, $normalizedOffset),
            ...array_slice($filterKeys, 0, $normalizedOffset),
        ]);

        return array_values(array_slice($rotatedFilterKeys, 0, $desiredCount));
    }
}
