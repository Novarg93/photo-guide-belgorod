<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Location;
use App\Models\Photographer;
use Inertia\Testing\AssertableInertia as Assert;

it('renders limited active blocks on welcome page', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'is_active' => true,
    ]);

    Category::factory()->count(4)->create([
        'is_active' => true,
    ]);

    Category::factory()->create([
        'name' => 'Hidden Category',
        'slug' => 'hidden-category',
        'is_active' => false,
    ]);

    Location::factory()->count(5)->create([
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    Location::factory()->create([
        'category_id' => $category->id,
        'name' => 'Hidden Location',
        'slug' => 'hidden-location',
        'is_active' => false,
    ]);

    Photographer::factory()->count(5)->create([
        'is_active' => true,
    ]);

    Photographer::factory()->create([
        'name' => 'Hidden Photographer',
        'is_active' => false,
    ]);

    Blog::factory()->count(4)->create([
        'is_active' => true,
    ]);

    Blog::factory()->create([
        'title' => 'Hidden Blog',
        'slug' => 'hidden-blog',
        'is_active' => false,
    ]);

    Faq::factory()->count(3)->create([
        'is_active' => true,
    ]);

    Faq::factory()->create([
        'question' => 'Hidden FAQ',
        'is_active' => false,
    ]);

    $response = $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('categories', 4)
            ->has('locations', 5)
            ->has('photographers', 5)
            ->has('blogs', 4)
            ->has('faqs', 3));

    expect(collect($response->inertiaProps('categories'))->pluck('name')->all())
        ->not->toContain('Hidden Category');
    expect(collect($response->inertiaProps('locations'))->pluck('name')->all())
        ->not->toContain('Hidden Location');
    expect(collect($response->inertiaProps('photographers'))->pluck('name')->all())
        ->not->toContain('Hidden Photographer');
    expect(collect($response->inertiaProps('blogs'))->pluck('title')->all())
        ->not->toContain('Hidden Blog');
    expect(collect($response->inertiaProps('faqs'))->pluck('question')->all())
        ->not->toContain('Hidden FAQ');
});
