<?php

use App\Models\Category;
use App\Models\Example;
use App\Models\Photo;
use Inertia\Testing\AssertableInertia as Assert;

it('shows active examples on category page', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    Example::factory()->create([
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
        'filter_option_keys' => ['moods.warm'],
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $coolExample->id,
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
