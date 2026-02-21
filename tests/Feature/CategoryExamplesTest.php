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
            ->where('activeFilters.mood', null)
            ->where('activeFilters.season', null)
            ->where('activeFilters.location', null)
            ->where('activeFilters.clothing', null));
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

it('filters examples by query params and returns active filters', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Filtered Match',
        'slug' => 'filtered-match',
        'mood' => 'Warm',
        'season_hint' => 'Autumn',
        'location_hint' => 'City center',
        'clothing_hint' => 'Casual',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Different Example',
        'slug' => 'different-example',
        'mood' => 'Bold',
        'season_hint' => 'Winter',
        'location_hint' => 'Studio loft',
        'clothing_hint' => 'Formal',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', [
        'slug' => $category->slug,
        'mood' => 'Warm',
        'season' => 'Autumn',
        'location' => 'City center',
        'clothing' => 'Casual',
    ]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->has('examples', 1)
            ->where('examples.0.title', 'Filtered Match')
            ->where('activeFilters.mood', 'Warm')
            ->where('activeFilters.season', 'Autumn')
            ->where('activeFilters.location', 'City center')
            ->where('activeFilters.clothing', 'Casual'));
});

it('returns filter options only for current category active examples', function () {
    $category = Category::factory()->create([
        'slug' => 'wedding',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'slug' => 'option-a',
        'mood' => 'Calm',
        'season_hint' => 'Spring',
        'location_hint' => 'Park',
        'clothing_hint' => 'Neutral',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'slug' => 'option-b',
        'mood' => 'Bold',
        'season_hint' => 'Winter',
        'location_hint' => 'Studio',
        'clothing_hint' => 'Classic',
        'is_active' => false,
    ]);

    $this->get(route('categories.show', ['slug' => $category->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('filterOptions.moods', ['Calm'])
            ->where('filterOptions.seasons', ['Spring'])
            ->where('filterOptions.locations', ['Park'])
            ->where('filterOptions.clothings', ['Neutral']));
});

it('applies preset values when preset is selected and filters are not explicit', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Golden Hour Walk',
        'slug' => 'golden-hour-walk',
        'summary' => 'Warm evening session.',
        'mood' => 'Warm',
        'season_hint' => 'Summer',
        'location_hint' => 'Park',
        'clothing_hint' => 'Casual',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Studio Mood',
        'slug' => 'studio-mood',
        'mood' => 'Cool',
        'season_hint' => 'Winter',
        'location_hint' => 'Studio',
        'clothing_hint' => 'Formal',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', [
        'slug' => $category->slug,
        'preset' => 'golden-hour-walk',
    ]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('activePreset.slug', 'golden-hour-walk')
            ->where('activeFilters.mood', 'Warm')
            ->where('activeFilters.season', 'Summer')
            ->where('activeFilters.location', 'Park')
            ->where('activeFilters.clothing', 'Casual')
            ->has('presets', 2)
            ->where('examples.0.title', 'Golden Hour Walk'));
});

it('lets explicit filter values override preset values', function () {
    $category = Category::factory()->create([
        'slug' => 'family',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Golden Hour Walk',
        'slug' => 'golden-hour-walk',
        'mood' => 'Warm',
        'season_hint' => 'Summer',
        'location_hint' => 'Park',
        'clothing_hint' => 'Casual',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Manual Override',
        'slug' => 'manual-override',
        'mood' => 'Cool',
        'season_hint' => 'Summer',
        'location_hint' => 'Park',
        'clothing_hint' => 'Casual',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', [
        'slug' => $category->slug,
        'preset' => 'golden-hour-walk',
        'mood' => 'Cool',
    ]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('activePreset.slug', 'golden-hour-walk')
            ->where('activeFilters.mood', 'Cool')
            ->where('activeFilters.season', 'Summer')
            ->where('activeFilters.location', 'Park')
            ->where('activeFilters.clothing', 'Casual')
            ->where('examples.0.title', 'Manual Override'));
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
