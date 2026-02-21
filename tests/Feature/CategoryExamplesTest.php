<?php

use App\Models\Category;
use App\Models\Example;
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
            ->where('examples.0.title', 'Golden Hour Walk'));
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
