<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Support\CategoryFilterSchema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class LocationSeeder extends Seeder
{
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

            foreach (Arr::random($locationNames, 4) as $index => $locationName) {
                $selectedFilters = [];

                if ($allowedFilterKeys !== []) {
                    $shuffled = $allowedFilterKeys;
                    shuffle($shuffled);
                    $selectedFilters = Arr::take($shuffled, random_int(1, min(3, count($shuffled))));
                }

                Location::query()->updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => "{$category->name}: {$locationName} ".($index + 1),
                    ],
                    [
                        'photo_path' => $placeholderPath,
                        'filter_option_keys' => $selectedFilters,
                        'is_active' => true,
                    ],
                );
            }
        });
    }
}
