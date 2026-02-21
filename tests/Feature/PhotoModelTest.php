<?php

use App\Models\Category;
use App\Models\Example;
use App\Models\Photo;

it('auto-generates photo title from filename when title is blank', function () {
    $category = Category::factory()->create();

    $photo = Photo::query()->create([
        'category_id' => $category->id,
        'example_id' => null,
        'title' => '',
        'path' => 'photos/custom-cover-name.png',
        'source_type' => 'stock',
        'source_url' => 'https://example.com/license/1',
        'author_name' => 'Author',
        'license' => 'stock',
        'permission_note' => null,
        'is_active' => false,
    ]);

    expect($photo->title)->toBe('custom-cover-name');
});

it('syncs photo category with example category when example is selected', function () {
    $category = Category::factory()->create();

    $example = Example::factory()->create([
        'category_id' => $category->id,
    ]);

    $otherCategory = Category::factory()->create();

    $photo = Photo::query()->create([
        'category_id' => $otherCategory->id,
        'example_id' => $example->id,
        'title' => 'Manual title',
        'path' => 'photos/example-photo.png',
        'source_type' => 'own',
        'source_url' => null,
        'author_name' => null,
        'license' => 'own',
        'permission_note' => null,
        'is_active' => true,
    ]);

    expect($photo->category_id)->toBe($category->id);
});

it('allows standalone photo without example when category is set', function () {
    $category = Category::factory()->create();

    $photo = Photo::query()->create([
        'category_id' => $category->id,
        'example_id' => null,
        'title' => 'Standalone photo',
        'path' => null,
        'source_type' => 'permission',
        'source_url' => null,
        'author_name' => null,
        'license' => 'permission',
        'permission_note' => 'Permission received via contract.',
        'is_active' => false,
    ]);

    expect($photo->example_id)->toBeNull()
        ->and($photo->category_id)->toBe($category->id);
});
