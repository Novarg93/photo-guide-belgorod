<?php

use App\Models\Category;
use App\Models\Location;
use App\Support\CategoryFilterSchema;
use Database\Seeders\CategorySeeder;
use Database\Seeders\LocationSeeder;
use Inertia\Testing\AssertableInertia as Assert;

it('shows all active locations on locations catalog page', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Central Park Belts',
        'slug' => 'central-park-belts',
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Hidden Spot',
        'is_active' => false,
    ]);

    $this->get(route('locations'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Locations')
            ->has('locations', 1)
            ->where('locations.0.name', 'Central Park Belts')
            ->where('locations.0.url', route('locations.show', ['slug' => 'central-park-belts'])));
});

it('shows recommended locations for category and current filters', function () {
    $category = Category::factory()->create([
        'name' => 'Wedding',
        'slug' => 'wedding',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Mood',
                'options' => [
                    ['name' => 'Romantic'],
                    ['name' => 'Documentary'],
                ],
            ],
        ],
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Romantic Garden',
        'filter_option_keys' => ['mood.romantic'],
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Documentary Street',
        'filter_option_keys' => ['mood.documentary'],
        'is_active' => true,
    ]);

    $this->get(route('categories.show', [
        'slug' => $category->slug,
        'filters' => ['mood.romantic'],
    ]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->has('locations', 1)
            ->where('locations.0.name', 'Romantic Garden'));
});

it('shows location page with photo session examples array', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    $location = Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Park Pobedy Belgorod',
        'slug' => 'park-pobedy-belgorod',
        'description' => 'Photo sessions in Park Pobedy Belgorod during all seasons.',
        'seo_title' => 'Photo sessions in Park Pobedy Belgorod',
        'seo_description' => 'Examples of photo sessions in Park Pobedy Belgorod and ideas for shooting.',
        'example_photo_paths' => [
            'photos/placeholder.svg',
            'locations/placeholder.svg',
        ],
        'is_active' => true,
    ]);

    $this->get(route('locations.show', ['slug' => $location->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('LocationShow')
            ->where('location.name', 'Park Pobedy Belgorod')
            ->has('location.example_photos', 2)
            ->where('metaTitle', 'Photo sessions in Park Pobedy Belgorod'));
});

it('seeds at least ten active locations for each category filter option', function () {
    $this->seed([
        CategorySeeder::class,
        LocationSeeder::class,
    ]);

    Category::query()
        ->select(['id', 'filter_groups'])
        ->get()
        ->each(function (Category $category): void {
            $allowedFilterKeys = CategoryFilterSchema::allowedOptionKeys($category->filter_groups);
            $coverage = array_fill_keys($allowedFilterKeys, 0);

            Location::query()
                ->where('category_id', $category->id)
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

            expect($coverage)->not->toBeEmpty();

            foreach ($coverage as $count) {
                expect($count)->toBeGreaterThanOrEqual(10);
            }
        });
});
