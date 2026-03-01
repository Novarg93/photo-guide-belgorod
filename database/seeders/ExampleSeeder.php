<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Example;
use App\Support\CategoryFilterSchema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exampleBlueprints = [
            [
                'title' => 'Golden Hour Promenade',
                'summary' => 'Soft evening light and calm movement through open urban spaces.',
            ],
            [
                'title' => 'Minimal Studio Frames',
                'summary' => 'Simple compositions and clean framing with focus on expressions.',
            ],
            [
                'title' => 'City Narrative Set',
                'summary' => 'Story-based sequence with architectural lines and natural interactions.',
            ],
            [
                'title' => 'Park Rhythm Session',
                'summary' => 'Dynamic pacing across tree lines, open grass areas, and walkways.',
            ],
            [
                'title' => 'Cozy Indoor Portraits',
                'summary' => 'Warm textures and intimate framing in an indoor setting.',
            ],
            [
                'title' => 'Editorial Contrast Story',
                'summary' => 'Higher contrast visual language with intentional posing and details.',
            ],
            [
                'title' => 'Weekend Casual Walk',
                'summary' => 'Relaxed pacing and lifestyle direction for natural candid moments.',
            ],
            [
                'title' => 'Fine Art Soft Light',
                'summary' => 'Delicate light shaping and layered compositions for a refined look.',
            ],
        ];

        $seasonPool = ['Spring', 'Summer', 'Autumn', 'Winter'];
        $locationPool = ['City center', 'Pine park', 'Studio loft', 'Riverside', 'Apartment interior'];
        $clothingPool = ['Casual', 'Classic', 'Neutral', 'Monochrome', 'Soft knit'];

        Category::query()->select(['id', 'name', 'slug', 'filter_groups'])->get()->each(function (Category $category) use (
            $exampleBlueprints,
            $seasonPool,
            $locationPool,
            $clothingPool,
        ): void {
            $groupOptionValues = collect($category->filter_groups ?? [])
                ->mapWithKeys(function (mixed $group): array {
                    if (! is_array($group) || ! isset($group['name']) || ! is_array($group['options'] ?? null)) {
                        return [];
                    }

                    $name = Str::slug((string) $group['name']);
                    $values = collect($group['options'])
                        ->map(fn (mixed $option): string => trim((string) (is_array($option) ? ($option['name'] ?? '') : '')))
                        ->filter()
                        ->values()
                        ->all();

                    return [$name => $values];
                });

            $moodOptions = collect($groupOptionValues->get('mood', []))
                ->whenEmpty(fn (Collection $collection): Collection => $collection->push('Calm', 'Bold', 'Natural'))
                ->values()
                ->all();
            $allowedFilterKeys = CategoryFilterSchema::allowedOptionKeys($category->filter_groups);

            foreach ($exampleBlueprints as $index => $blueprint) {
                $title = "{$category->name}: {$blueprint['title']}";
                $slug = Str::slug("{$category->slug}-example-{$index}");
                $mood = $moodOptions[$index % count($moodOptions)];
                $locationHint = $locationPool[$index % count($locationPool)];
                $seasonHint = $seasonPool[$index % count($seasonPool)];
                $clothingHint = $clothingPool[$index % count($clothingPool)];
                $presetFilters = $this->buildPresetFilters($allowedFilterKeys, $index);

                Example::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $category->id,
                        'title' => $title,
                        'summary' => $blueprint['summary'],
                        'filter_option_keys' => $presetFilters,
                        'mood' => $mood,
                        'location_hint' => $locationHint,
                        'season_hint' => $seasonHint,
                        'clothing_hint' => $clothingHint,
                        'is_active' => true,
                    ],
                );
            }
        });
    }

    /**
     * @param  array<int, string>  $allowedFilterKeys
     * @return array<int, string>
     */
    private function buildPresetFilters(array $allowedFilterKeys, int $offset): array
    {
        if ($allowedFilterKeys === []) {
            return [];
        }

        $desiredCount = min(count($allowedFilterKeys), max(1, min(4, 2 + ($offset % 3))));
        $rotatedFilterKeys = $this->rotateFilterKeys($allowedFilterKeys, $offset);

        return array_values(array_slice($rotatedFilterKeys, 0, $desiredCount));
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
