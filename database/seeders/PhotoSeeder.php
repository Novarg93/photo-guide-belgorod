<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Example;
use App\Models\Photo;
use App\Support\CategoryFilterSchema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PhotoSeeder extends Seeder
{
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

        Example::query()->select(['id', 'category_id'])->get()->each(function (Example $example) use ($activeSources, $filterKeysByCategory, $placeholderPath): void {
            $allowedFilterKeys = $filterKeysByCategory->get($example->category_id, []);
            $selectedFilterKeys = [];

            if ($allowedFilterKeys !== []) {
                shuffle($allowedFilterKeys);
                $selectedFilterKeys = Arr::take($allowedFilterKeys, random_int(1, min(3, count($allowedFilterKeys))));
            }

            foreach ([1, 2] as $slot) {
                $sourceType = $activeSources[$slot - 1];

                Photo::query()->updateOrCreate(
                    [
                        'example_id' => $example->id,
                        'source_type' => $sourceType,
                        'source_url' => "https://example.com/library/{$example->id}/{$slot}",
                    ],
                    [
                        'title' => "Example {$example->id} Photo {$slot}",
                        'category_id' => $example->category_id,
                        'filter_option_keys' => $selectedFilterKeys,
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
    }
}
