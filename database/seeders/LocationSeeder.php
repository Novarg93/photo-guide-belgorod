<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Support\CategoryFilterSchema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    private const TARGET_LOCATIONS_PER_FILTER = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $placeholderPath = 'locations/placeholder.svg';

        if (! Storage::disk('public')->exists($placeholderPath)) {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#e2e8f0"/><stop offset="100%" stop-color="#cbd5e1"/></linearGradient></defs><rect width="1200" height="800" fill="url(#g)"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="48" fill="#334155">Location Placeholder</text></svg>';
            Storage::disk('public')->put($placeholderPath, $svg);
        }

        $locationNames = [
            'Central Park Belts',
            'Modern Studio Loft',
            'Historic City Courtyard',
            'Riverside Walk',
            'Botanical Garden Path',
            'Minimal White Hall',
        ];

        Category::query()->select(['id', 'name', 'filter_groups'])->get()->each(function (Category $category) use ($locationNames, $placeholderPath): void {
            $allowedFilterKeys = CategoryFilterSchema::allowedOptionKeys($category->filter_groups);

            $desiredBaseCount = max(count($locationNames), count($allowedFilterKeys));

            foreach (range(1, $desiredBaseCount) as $index) {
                $locationName = $locationNames[($index - 1) % count($locationNames)];
                $selectedFilters = $this->buildMixedFilterKeys($allowedFilterKeys, [], $category->id + $index);

                Location::query()->updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => "{$category->name}: {$locationName} {$index}",
                    ],
                    [
                        'slug' => Str::slug("category-{$category->id}-{$locationName}-{$index}"),
                        'description' => "Location {$locationName} in Belgorod for {$category->name} photo sessions.",
                        'seo_title' => "{$locationName} - photo sessions in Belgorod",
                        'seo_description' => "Ideas and examples for photo sessions at {$locationName} in Belgorod: poses, style, and shooting scenarios.",
                        'photo_path' => $placeholderPath,
                        'example_photo_paths' => [
                            $placeholderPath,
                            $placeholderPath,
                            $placeholderPath,
                        ],
                        'filter_option_keys' => $selectedFilters,
                        'is_active' => true,
                    ],
                );
            }

            if ($allowedFilterKeys === []) {
                return;
            }

            $coverage = $this->calculateCoverage($category->id, $allowedFilterKeys);
            $slot = $desiredBaseCount + 1;
            $maxSlots = count($allowedFilterKeys) * self::TARGET_LOCATIONS_PER_FILTER * 2;

            while ($this->needsMoreCoverage($coverage) && $slot <= $maxSlots) {
                $missingFilterKeys = array_keys(array_filter(
                    $coverage,
                    fn (int $count): bool => $count < self::TARGET_LOCATIONS_PER_FILTER,
                ));
                $primaryFilterKey = $missingFilterKeys[($slot - 1) % count($missingFilterKeys)];
                $locationName = $locationNames[($slot - 1) % count($locationNames)];
                $mixedFilterKeys = $this->buildMixedFilterKeys($allowedFilterKeys, [$primaryFilterKey], $category->id + $slot);
                $title = "{$category->name}: {$locationName} {$slot}";

                if (Location::query()
                    ->where('category_id', $category->id)
                    ->where('name', $title)
                    ->exists()) {
                    $slot++;

                    continue;
                }

                Location::query()->create([
                    'category_id' => $category->id,
                    'name' => $title,
                    'slug' => Str::slug("category-{$category->id}-{$locationName}-{$slot}"),
                    'description' => "Location {$locationName} in Belgorod for {$category->name} photo sessions.",
                    'seo_title' => "{$locationName} - photo sessions in Belgorod",
                    'seo_description' => "Ideas and examples for photo sessions at {$locationName} in Belgorod: poses, style, and shooting scenarios.",
                    'photo_path' => $placeholderPath,
                    'example_photo_paths' => [
                        $placeholderPath,
                        $placeholderPath,
                        $placeholderPath,
                    ],
                    'filter_option_keys' => $mixedFilterKeys,
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

        Location::query()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->select(['filter_option_keys'])
            ->get()
            ->each(function (Location $location) use (&$coverage): void {
                foreach ($location->filter_option_keys ?? [] as $filterKey) {
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
            if ($count < self::TARGET_LOCATIONS_PER_FILTER) {
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
