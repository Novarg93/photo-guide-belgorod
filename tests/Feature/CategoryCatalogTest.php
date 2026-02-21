<?php

use App\Models\Category;
use Inertia\Testing\AssertableInertia as Assert;

it('shows welcome page with category selection call to action', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('metaTitle', 'Photo sessions in Belgorod'));
});

it('shows only active categories in catalog', function () {
    Category::factory()->create([
        'name' => 'Active Category',
        'slug' => 'active-category',
        'is_active' => true,
    ]);

    Category::factory()->create([
        'name' => 'Inactive Category',
        'slug' => 'inactive-category',
        'is_active' => false,
    ]);

    $this->get(route('catalog'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Catalog')
            ->has('categories', 1)
            ->where('categories.0.name', 'Active Category')
            ->where('categories.0.slug', 'active-category'));
});

it('shows category detail by slug', function () {
    $category = Category::factory()->create([
        'name' => 'Family',
        'slug' => 'family',
        'description' => 'Family sessions in Belgorod.',
        'is_active' => true,
    ]);

    $this->get(route('categories.show', $category->slug))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CategoryShow')
            ->where('category.name', 'Family')
            ->where('category.description', 'Family sessions in Belgorod.'));
});

it('returns not found for inactive category page', function () {
    $category = Category::factory()->create([
        'slug' => 'hidden-category',
        'is_active' => false,
    ]);

    $this->get(route('categories.show', $category->slug))
        ->assertNotFound();
});

it('auto-generates slug on create when it is not provided', function () {
    $category = Category::query()->create([
        'name' => 'Свадебная съёмка',
        'description' => 'Test',
        'is_active' => true,
    ]);

    expect($category->slug)->not->toBeEmpty();
});

it('regenerates slug when name changes and slug was not manually edited', function () {
    $category = Category::factory()->create([
        'name' => 'Old Name',
        'slug' => 'old-name',
    ]);

    $category->update([
        'name' => 'New Name',
    ]);

    expect($category->fresh()->slug)->toBe('new-name');
});

it('keeps manually edited slug when name changes', function () {
    $category = Category::factory()->create([
        'name' => 'Initial Name',
        'slug' => 'initial-name',
    ]);

    $category->update([
        'name' => 'Updated Name',
        'slug' => 'custom-manual-slug',
    ]);

    expect($category->fresh()->slug)->toBe('custom-manual-slug');
});
