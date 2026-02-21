<?php

use App\Models\Brief;
use App\Models\Category;
use App\Models\Example;
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
        'selected_example_ids' => [$selectedExample->id],
    ]);

    $brief = Brief::query()->latest('id')->first();

    expect($brief)->not->toBeNull()
        ->and($brief?->category_id)->toBe($category->id)
        ->and($brief?->filters['mood'])->toBe('Warm')
        ->and($brief?->filters['season'])->toBeNull()
        ->and($brief?->selected_example_ids)->toBe([$selectedExample->id]);

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
            ->where('shareUrl', route('brief.show', ['token' => $brief->public_token])));
});

it('shows up to six filtered examples when selected examples are empty', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Zeta',
        'slug' => 'zeta',
        'mood' => 'Warm',
        'season_hint' => 'Autumn',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'Alpha',
        'slug' => 'alpha',
        'mood' => 'Warm',
        'season_hint' => 'Autumn',
        'is_active' => true,
    ]);

    Example::factory()->create([
        'category_id' => $category->id,
        'title' => 'No Match',
        'slug' => 'no-match',
        'mood' => 'Cold',
        'season_hint' => 'Winter',
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
