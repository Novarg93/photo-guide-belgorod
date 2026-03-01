<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Example;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
