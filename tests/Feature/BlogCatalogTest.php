<?php

use App\Models\Blog;
use Inertia\Testing\AssertableInertia as Assert;

it('shows only active blogs on blogs page', function () {
    Blog::factory()->create([
        'title' => 'Active Blog',
        'slug' => 'active-blog',
        'is_active' => true,
    ]);

    Blog::factory()->create([
        'title' => 'Hidden Blog',
        'slug' => 'hidden-blog',
        'is_active' => false,
    ]);

    $this->get(route('blogs'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blogs')
            ->has('blogs', 1)
            ->where('blogs.0.title', 'Active Blog')
            ->where('blogs.0.url', route('blogs.show', ['slug' => 'active-blog'])));
});

it('shows blog detail page by slug', function () {
    $blog = Blog::factory()->create([
        'title' => 'Golden Hour Guide',
        'slug' => 'golden-hour-guide',
        'excerpt' => 'How to work with evening light.',
        'content' => 'Full blog content for details.',
        'seo_title' => 'Golden Hour Guide for Photo Sessions',
        'seo_description' => 'Tips for planning sessions at golden hour.',
        'is_active' => true,
    ]);

    $this->get(route('blogs.show', ['slug' => $blog->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BlogShow')
            ->where('blog.title', 'Golden Hour Guide')
            ->where('blog.slug', 'golden-hour-guide')
            ->where('metaTitle', 'Golden Hour Guide for Photo Sessions'));
});

it('returns not found for inactive blog detail page', function () {
    $blog = Blog::factory()->create([
        'title' => 'Private Blog',
        'slug' => 'private-blog',
        'is_active' => false,
    ]);

    $this->get(route('blogs.show', ['slug' => $blog->slug]))
        ->assertNotFound();
});

it('seeds ten default blogs', function () {
    $this->seed(\Database\Seeders\BlogSeeder::class);

    expect(Blog::query()->count())->toBeGreaterThanOrEqual(10);
});
