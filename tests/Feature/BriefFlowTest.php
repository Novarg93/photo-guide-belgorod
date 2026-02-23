<?php

use App\Models\Brief;
use App\Models\Category;
use App\Models\Example;
use App\Models\Location;
use App\Models\Photo;
use Inertia\Testing\AssertableInertia as Assert;

it('creates brief from category filters and redirects to public brief page', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    $selectedExample = Example::factory()->create([
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    $response = $this->post(route('brief.store'), [
        'category_slug' => $category->slug,
        'mood' => 'Warm',
        'season' => null,
        'location' => 'City center',
        'clothing' => null,
        'people_count' => '3-4',
        'notes' => 'No studio. Natural light only.',
        'selected_example_ids' => [$selectedExample->id],
    ]);

    $brief = Brief::query()->latest('id')->first();

    expect($brief)->not->toBeNull()
        ->and($brief?->category_id)->toBe($category->id)
        ->and($brief?->filters['mood'])->toBe('Warm')
        ->and($brief?->filters['season'])->toBeNull()
        ->and($brief?->selected_example_ids)->toBe([$selectedExample->id])
        ->and($brief?->people_count)->toBe('3-4')
        ->and($brief?->notes)->toBe('No studio. Natural light only.');

    $response->assertRedirect(route('brief.show', ['token' => $brief->public_token]));
});

it('shows public brief page with selected examples only and share link', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Warm Match',
        'slug' => 'warm-match',
        'mood' => 'Warm',
        'season_hint' => 'Autumn',
        'is_active' => true,
    ]);

    $selectedExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Selected First',
        'slug' => 'selected-first',
        'mood' => 'Cold',
        'season_hint' => 'Winter',
        'is_active' => true,
    ]);

    $brief = Brief::query()->create([
        'category_id' => $category->id,
        'public_token' => '11111111-1111-4111-8111-111111111111',
        'filters' => [
            'mood' => 'Warm',
            'season' => 'Autumn',
            'location' => null,
            'clothing' => null,
        ],
        'selected_example_ids' => [$selectedExample->id],
        'people_count' => '2',
        'notes' => 'Focus on candid photos.',
    ]);

    $this->get(route('brief.show', ['token' => $brief->public_token]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BriefShow')
            ->where('category.slug', 'family')
            ->where('filters.mood', 'Warm')
            ->has('examples', 1)
            ->where('examples.0.title', 'Selected First')
            ->where('selectedExampleIds.0', $selectedExample->id)
            ->where('peopleCount', '2')
            ->where('notes', 'Focus on candid photos.')
            ->where('shareUrl', route('brief.show', ['token' => $brief->public_token])));
});

it('shows up to six filtered examples when selected examples are empty', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    $zetaExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Zeta',
        'slug' => 'zeta',
        'mood' => 'Warm',
        'season_hint' => 'Autumn',
        'is_active' => true,
    ]);

    $alphaExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Alpha',
        'slug' => 'alpha',
        'mood' => 'Warm',
        'season_hint' => 'Autumn',
        'is_active' => true,
    ]);

    $noMatchExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'No Match',
        'slug' => 'no-match',
        'mood' => 'Cold',
        'season_hint' => 'Winter',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $zetaExample->id,
        'path' => 'photos/zeta.svg',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $alphaExample->id,
        'path' => 'photos/alpha.svg',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $noMatchExample->id,
        'path' => 'photos/no-match.svg',
        'is_active' => true,
    ]);

    $brief = Brief::query()->create([
        'category_id' => $category->id,
        'public_token' => '22222222-2222-4222-8222-222222222222',
        'filters' => [
            'mood' => 'Warm',
            'season' => 'Autumn',
            'location' => null,
            'clothing' => null,
        ],
        'selected_example_ids' => [],
    ]);

    $this->get(route('brief.show', ['token' => $brief->public_token]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BriefShow')
            ->where('category.slug', 'family')
            ->where('filters.mood', 'Warm')
            ->where('selectedExampleIds', [])
            ->has('examples', 2)
            ->where('examples.0.title', 'Alpha')
            ->where('examples.1.title', 'Zeta')
            ->where('shareUrl', route('brief.show', ['token' => $brief->public_token])));
});

it('stores and returns editing preferences on brief show', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    $selectedExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Selected',
        'slug' => 'selected',
        'is_active' => true,
    ]);

    $response = $this->post(route('brief.store'), [
        'category_slug' => $category->slug,
        'mood' => null,
        'season' => null,
        'location' => null,
        'clothing' => null,
        'people_count' => null,
        'notes' => null,
        'retouch_preference' => 'Natural',
        'color_style' => 'Film',
        'selected_example_ids' => [$selectedExample->id],
    ]);

    $brief = Brief::query()->latest('id')->firstOrFail();

    expect($brief->retouch_preference)->toBe('Natural')
        ->and($brief->color_style)->toBe('Film');

    $response->assertRedirect(route('brief.show', ['token' => $brief->public_token]));

    $this->get(route('brief.show', ['token' => $brief->public_token]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BriefShow')
            ->where('retouchPreference', 'Natural')
            ->where('colorStyle', 'Film'));
});

it('shows selected standalone photos when brief has selected photo ids', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    $selectedPhoto = Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => null,
        'title' => 'Selected Standalone',
        'path' => 'photos/selected-standalone.svg',
        'is_active' => true,
    ]);

    $brief = Brief::query()->create([
        'category_id' => $category->id,
        'public_token' => '33333333-3333-4333-8333-333333333333',
        'filters' => [
            'mood' => null,
            'season' => null,
            'location' => null,
            'clothing' => null,
            'active_filter_option_keys' => [],
            'selected_photo_ids' => [$selectedPhoto->id],
        ],
        'selected_example_ids' => [],
    ]);

    $this->get(route('brief.show', ['token' => $brief->public_token]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BriefShow')
            ->has('examples', 1)
            ->where('examples.0.title', 'Selected Standalone')
            ->where('examples.0.image_url', fn (string $url): bool => str_contains($url, '/storage/photos/selected-standalone.svg')));
});

it('uses active filter option keys when no explicit cards are selected', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Mood',
                'options' => [
                    ['name' => 'Warm'],
                    ['name' => 'Cool'],
                ],
            ],
        ],
    ]);

    $warmExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Warm Example',
        'slug' => 'warm-example',
        'is_active' => true,
    ]);

    $coolExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Cool Example',
        'slug' => 'cool-example',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $warmExample->id,
        'path' => 'photos/warm-example.svg',
        'filter_option_keys' => ['mood.warm'],
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $coolExample->id,
        'path' => 'photos/cool-example.svg',
        'filter_option_keys' => ['mood.cool'],
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => null,
        'title' => 'Warm Standalone',
        'path' => 'photos/warm-standalone.svg',
        'filter_option_keys' => ['mood.warm'],
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Warm Location',
        'photo_path' => 'locations/warm-location.svg',
        'filter_option_keys' => ['mood.warm'],
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Cool Location',
        'photo_path' => 'locations/cool-location.svg',
        'filter_option_keys' => ['mood.cool'],
        'is_active' => true,
    ]);

    $brief = Brief::query()->create([
        'category_id' => $category->id,
        'public_token' => '44444444-4444-4444-8444-444444444444',
        'filters' => [
            'mood' => null,
            'season' => null,
            'location' => null,
            'clothing' => null,
            'active_filter_option_keys' => ['mood.warm'],
            'selected_photo_ids' => [],
        ],
        'selected_example_ids' => [],
    ]);

    $this->get(route('brief.show', ['token' => $brief->public_token]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BriefShow')
            ->where('locationFilterOptionLabels.0', 'Mood: Warm')
            ->where('filters.active_filter_option_keys.0', 'mood.warm')
            ->has('examples', 2)
            ->where('examples.0.title', 'Warm Example')
            ->where('examples.1.title', 'Warm Standalone')
            ->has('locations', 1)
            ->where('locations.0.name', 'Warm Location'));
});

it('filters recommended locations by selected cards when active filter keys are empty', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
        'filter_groups' => [
            [
                'name' => 'Mood',
                'options' => [
                    ['name' => 'Warm'],
                    ['name' => 'Cool'],
                ],
            ],
        ],
    ]);

    $selectedExample = Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Selected Warm Example',
        'slug' => 'selected-warm-example',
        'is_active' => true,
    ]);

    Photo::factory()->create([
        'category_id' => $category->id,
        'example_id' => $selectedExample->id,
        'path' => 'photos/selected-warm-example.svg',
        'filter_option_keys' => ['mood.warm'],
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Warm Location',
        'photo_path' => 'locations/warm-location.svg',
        'filter_option_keys' => ['mood.warm'],
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Cool Location',
        'photo_path' => 'locations/cool-location.svg',
        'filter_option_keys' => ['mood.cool'],
        'is_active' => true,
    ]);

    $brief = Brief::query()->create([
        'category_id' => $category->id,
        'public_token' => '55555555-5555-4555-8555-555555555555',
        'filters' => [
            'mood' => null,
            'season' => null,
            'location' => null,
            'clothing' => null,
            'active_filter_option_keys' => [],
            'selected_photo_ids' => [],
        ],
        'selected_example_ids' => [$selectedExample->id],
    ]);

    $this->get(route('brief.show', ['token' => $brief->public_token]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BriefShow')
            ->where('locationFilterOptionLabels.0', 'Mood: Warm')
            ->has('locations', 1)
            ->where('locations.0.name', 'Warm Location'));
});
