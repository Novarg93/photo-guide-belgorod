<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Example;
use App\Models\Photo;
use App\Support\CategoryFilterSchema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PhotoSeeder extends Seeder
{
    private const ACTIVE_EXAMPLE_PHOTOS_PER_EXAMPLE = 4;

    private const TARGET_PHOTOS_PER_FILTER = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $placeholderPath = 'photos/placeholder.svg';
        $activeSources = ['stock', 'own'];

        if (! Storage::disk('public')->exists($placeholderPath)) {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="800" viewBox="0 0 600 800"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#e4e4e7"/><stop offset="100%" stop-color="#d4d4d8"/></linearGradient></defs><rect width="600" height="800" fill="url(#g)"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" fill="#3f3f46">Photo Placeholder</text></svg>';
            Storage::disk('public')->put($placeholderPath, $svg);
        }

        $filterKeysByCategory = Category::query()
            ->select(['id', 'filter_groups'])
            ->get()
            ->mapWithKeys(fn (Category $category): array => [
                $category->id => CategoryFilterSchema::allowedOptionKeys($category->filter_groups),
            ]);

        Example::query()->select(['id', 'category_id', 'filter_option_keys'])->get()->each(function (Example $example) use ($activeSources, $filterKeysByCategory, $placeholderPath): void {
            $allowedFilterKeys = $filterKeysByCategory->get($example->category_id, []);
            $selectedFilterKeys = is_array($example->filter_option_keys) ? $example->filter_option_keys : [];

            foreach (range(1, self::ACTIVE_EXAMPLE_PHOTOS_PER_EXAMPLE) as $slot) {
                $sourceType = $activeSources[($slot - 1) % count($activeSources)];

                Photo::query()->updateOrCreate(
                    [
                        'example_id' => $example->id,
                        'source_type' => $sourceType,
                        'source_url' => "https://example.com/library/{$example->id}/{$slot}",
                    ],
                    [
                        'title' => "Example {$example->id} Photo {$slot}",
                        'category_id' => $example->category_id,
                        'filter_option_keys' => $this->buildMixedFilterKeys(
                            $allowedFilterKeys,
                            $selectedFilterKeys,
                            $example->id + $slot,
                        ),
                        'path' => $placeholderPath,
                        'author_name' => 'Seeded Library',
                        'license' => $sourceType === 'stock' ? 'stock' : 'own',
                        'permission_note' => null,
                        'is_active' => true,
                    ],
                );
            }

            Photo::query()->updateOrCreate(
                [
                    'example_id' => $example->id,
                    'source_type' => 'permission',
                    'source_url' => null,
                ],
                [
                    'title' => "Permission record for Example {$example->id}",
                    'category_id' => $example->category_id,
                    'filter_option_keys' => [],
                    'path' => null,
                    'author_name' => null,
                    'license' => 'permission',
                    'permission_note' => 'Permission document pending upload.',
                    'is_active' => false,
                ],
            );
        });

        Category::query()->select(['id', 'name', 'filter_groups'])->get()->each(function (Category $category) use ($placeholderPath): void {
            $allowedFilterKeys = CategoryFilterSchema::allowedOptionKeys($category->filter_groups);

            if ($allowedFilterKeys === []) {
                return;
            }

            $coverage = $this->calculateCoverage($category->id, $allowedFilterKeys);
            $slot = 1;
            $maxSlots = count($allowedFilterKeys) * self::TARGET_PHOTOS_PER_FILTER * 2;

            while ($this->needsMoreCoverage($coverage) && $slot <= $maxSlots) {
                $title = "{$category->name} Mixed Filter {$slot}";

                if (Photo::query()
                    ->where('category_id', $category->id)
                    ->whereNull('example_id')
                    ->where('title', $title)
                    ->exists()) {
                    $slot++;

                    continue;
                }

                $missingFilterKeys = array_keys(array_filter(
                    $coverage,
                    fn (int $count): bool => $count < self::TARGET_PHOTOS_PER_FILTER,
                ));
                $primaryFilterKey = $missingFilterKeys[($slot - 1) % count($missingFilterKeys)];
                $mixedFilterKeys = $this->buildMixedFilterKeys($allowedFilterKeys, [$primaryFilterKey], $category->id + $slot);

                Photo::query()->create([
                    'title' => $title,
                    'category_id' => $category->id,
                    'filter_option_keys' => $mixedFilterKeys,
                    'example_id' => null,
                    'path' => $placeholderPath,
                    'source_type' => 'own',
                    'source_url' => null,
                    'author_name' => 'Seeded Coverage',
                    'license' => 'own',
                    'permission_note' => null,
                    'is_active' => true,
                ]);

                foreach ($mixedFilterKeys as $filterKey) {
                    $coverage[$filterKey]++;
                }

                $slot++;
            }
        });
    }

    /**
     * @param  array<int, string>  $allowedFilterKeys
     * @param  array<int, string>  $priorityFilterKeys
     * @return array<int, string>
     */
    private function buildMixedFilterKeys(array $allowedFilterKeys, array $priorityFilterKeys, int $offset): array
    {
        if ($allowedFilterKeys === []) {
            return [];
        }

        $selectedFilterKeys = [];

        foreach ($priorityFilterKeys as $filterKey) {
            if (in_array($filterKey, $allowedFilterKeys, true) && ! in_array($filterKey, $selectedFilterKeys, true)) {
                $selectedFilterKeys[] = $filterKey;
            }
        }

        $desiredCount = min(count($allowedFilterKeys), max(count($selectedFilterKeys), min(4, 2 + ($offset % 3))));

        foreach ($this->rotateFilterKeys($allowedFilterKeys, $offset) as $filterKey) {
            if (! in_array($filterKey, $selectedFilterKeys, true)) {
                $selectedFilterKeys[] = $filterKey;
            }

            if (count($selectedFilterKeys) >= $desiredCount) {
                break;
            }
        }

        return array_values($selectedFilterKeys);
    }

    /**
     * @param  array<int, string>  $allowedFilterKeys
     * @return array<string, int>
     */
    private function calculateCoverage(int $categoryId, array $allowedFilterKeys): array
    {
        $coverage = array_fill_keys($allowedFilterKeys, 0);

        Photo::query()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->select(['filter_option_keys'])
            ->get()
            ->each(function (Photo $photo) use (&$coverage): void {
                foreach ($photo->filter_option_keys ?? [] as $filterKey) {
                    if (array_key_exists($filterKey, $coverage)) {
                        $coverage[$filterKey]++;
                    }
                }
            });

        return $coverage;
    }

    /**
     * @param  array<string, int>  $coverage
     */
    private function needsMoreCoverage(array $coverage): bool
    {
        foreach ($coverage as $count) {
            if ($count < self::TARGET_PHOTOS_PER_FILTER) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $filterKeys
     * @return array<int, string>
     */
    private function rotateFilterKeys(array $filterKeys, int $offset): array
    {
        $total = count($filterKeys);

        if ($total <= 1) {
            return $filterKeys;
        }

        $normalizedOffset = $offset % $total;

        return array_values([
            ...array_slice($filterKeys, $normalizedOffset),
            ...array_slice($filterKeys, 0, $normalizedOffset),
        ]);
    }
}
