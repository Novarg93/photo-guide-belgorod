<?php

use App\Models\Category;
use App\Models\Example;
use App\Models\Photo;
use App\Support\CategoryFilterSchema;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ExampleSeeder;
use Database\Seeders\PhotoSeeder;
use Inertia\Testing\AssertableInertia as Assert;

it('shows active examples on category page', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    $activeExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Golden Hour Walk',
        'slug' => 'golden-hour-walk',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Hidden Draft Example',
        'slug' => 'hidden-draft-example',
        'is_active' => false,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $activeExample->id,
        'path' => 'photos/golden-hour-walk.svg',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', ['slug' => $category->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->has('examples', 1)
            ->where('examples.0.title', 'Golden Hour Walk')
            ->where('activeFilterOptionKeys', []));
});

it('belongs examples to their category', function () {
    $category = Category::factory()->create();
    $example = Example::factory()->create([
        'category_id' => $category->id,
    ]);

    expect($example->category->is($category))->toBeTrue();
});

it('auto-generates example slug on create when it is not provided', function () {
    $category = Category::factory()->create();

    $example = Example::query()->create([
        'category_id' => $category->id,
        'title' => 'Sunset Portrait Set',
        'is_active' => true,
    ]);

    expect($example->slug)->toBe('sunset-portrait-set');
});

it('returns category filter groups to frontend', function () {
    $category = Category::factory()->create([
        'slug' => 'wedding',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Moods',
                'options' => [
                    ['name' => 'Calm'],
                    ['name' => 'Bold'],
                ],
            ],
        ],
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'slug' => 'option-a',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', ['slug' => $category->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('filterGroups.0.label', 'Moods')
            ->where('filterGroups.0.options.0.label', 'Calm')
            ->where('filterGroups.0.options.1.label', 'Bold'));
});

it('filters examples by selected photo filter options', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Moods',
                'options' => [
                    ['name' => 'Warm'],
                    ['name' => 'Cool'],
                ],
            ],
        ],
    ]);

    $warmExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Warm Match',
        'slug' => 'warm-match',
        'is_active' => true,
    ]);

    $coolExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Cool Match',
        'slug' => 'cool-match',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $warmExample->id,
        'path' => 'photos/warm-match.svg',
        'filter_option_keys' => ['moods.warm'],
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $coolExample->id,
        'path' => 'photos/cool-match.svg',
        'filter_option_keys' => ['moods.cool'],
        'is_active' => true,
    ]);

    $this->get(route('categories.show', [
        'slug' => $category->slug,
        'filters' => ['moods.warm'],
    ]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->has('examples', 1)
            ->where('examples.0.title', 'Warm Match')
            ->where('activeFilterOptionKeys', ['moods.warm']));
});

it('applies preset filters when preset is selected without explicit filters', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Moods',
                'options' => [
                    ['name' => 'Warm'],
                    ['name' => 'Cool'],
                ],
            ],
        ],
    ]);

    $preset = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Warm Preset',
        'slug' => 'warm-preset',
        'filter_option_keys' => ['moods.warm'],
        'is_active' => true,
    ]);

    $warmExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Warm Match',
        'slug' => 'warm-match',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Cool Match',
        'slug' => 'cool-match',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $warmExample->id,
        'path' => 'photos/warm-preset.svg',
        'filter_option_keys' => ['moods.warm'],
        'is_active' => true,
    ]);

    $this->get(route('categories.show', [
        'slug' => $category->slug,
        'preset' => $preset->slug,
    ]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('activePreset.slug', 'warm-preset')
            ->where('activeFilterOptionKeys', ['moods.warm']));
});

it('switches to custom when explicit filters differ from preset filters', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Moods',
                'options' => [
                    ['name' => 'Warm'],
                    ['name' => 'Cool'],
                ],
            ],
        ],
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Warm Preset',
        'slug' => 'warm-preset',
        'filter_option_keys' => ['moods.warm'],
        'is_active' => true,
    ]);

    $this->get(route('categories.show', [
        'slug' => $category->slug,
        'preset' => 'warm-preset',
        'filters' => ['moods.cool'],
    ]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('activePreset', null)
            ->where('activeFilterOptionKeys', ['moods.cool']));
});

it('prefers active photo url over fallback image url for category cards', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
    ]);

    $example = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'With Photo Cover',
        'slug' => 'with-photo-cover',
        'cover_image' => null,
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'example_id' => $example->id,
        'path' => 'photos/cover-test.svg',
        'source_type' => 'stock',
        'license' => 'stock',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', ['slug' => $category->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('examples.0.title', 'With Photo Cover')
            ->where('examples.0.image_url', fn (string $url): bool => str_contains($url, '/storage/photos/cover-test.svg')));
});

it('shows standalone active photos for category', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => null,
        'title' => 'Standalone Family Photo',
        'path' => 'photos/family-standalone.svg',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', ['slug' => $category->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->has('examples', 1)
            ->where('examples.0.title', 'Standalone Family Photo')
            ->where('examples.0.example_id', null)
            ->where('examples.0.image_url', fn (string $url): bool => str_contains($url, '/storage/photos/family-standalone.svg')));
});

it('returns filter labels for standalone photo cards on category page', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Mood',
                'options' => [
                    ['name' => 'Warm'],
                    ['name' => 'Calm'],
                ],
            ],
        ],
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => null,
        'title' => 'Standalone warm photo',
        'path' => 'photos/standalone-warm.svg',
        'filter_option_keys' => ['mood.warm', 'mood.calm'],
        'is_active' => true,
    ]);

    $this->get(route('categories.show', ['slug' => $category->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('examples.0.title', 'Standalone warm photo')
            ->where('examples.0.filter_option_labels', ['Mood: Warm', 'Mood: Calm']));
});

it('seeds at least ten active photos for each category filter option', function () {
    $this->seed([
        CategorySeeder::class,
        ExampleSeeder::class,
        PhotoSeeder::class,
    ]);

    Category::query()
        ->select(['id', 'filter_groups'])
        ->get()
        ->each(function (Category $category): void {
            $allowedFilterKeys = CategoryFilterSchema::allowedOptionKeys($category->filter_groups);
            $coverage = array_fill_keys($allowedFilterKeys, 0);

            Photo::query()
                ->where('category_id', $category->id)
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

            expect($coverage)->not->toBeEmpty();

            foreach ($coverage as $count) {
                expect($count)->toBeGreaterThanOrEqual(10);
            }
        });
});
