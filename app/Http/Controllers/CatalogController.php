<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function welcome(): Response
    {
        return Inertia::render('Welcome', [
            'metaTitle' => 'Photo sessions in Belgorod',
            'metaDescription' => 'Choose a photo session category in Belgorod and continue to the next planning step.',
        ]);
    }

    public function index(): Response
    {
        $categories = Category::query()
            ->select(['id', 'name', 'slug', 'description'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'url' => route('categories.show', ['slug' => $category->slug]),
            ]);

        return Inertia::render('Catalog', [
            'categories' => $categories,
            'metaTitle' => 'Photo Session Catalog',
            'metaDescription' => 'Choose a category and continue to the next planning step.',
        ]);
    }

    public function show(string $slug): Response
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $examples = $category->examples()
            ->select([
                'id',
                'title',
                'summary',
                'mood',
                'location_hint',
                'season_hint',
                'clothing_hint',
                'cover_image',
            ])
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            ->map(fn ($example): array => [
                'id' => $example->id,
                'title' => $example->title,
                'summary' => $example->summary,
                'mood' => $example->mood,
                'location_hint' => $example->location_hint,
                'season_hint' => $example->season_hint,
                'clothing_hint' => $example->clothing_hint,
                'image_url' => $example->image_url,
            ]);

        return Inertia::render('CategoryShow', [
            'category' => [
                'name' => $category->name,
                'description' => $category->description,
            ],
            'examples' => $examples,
            'metaTitle' => $category->seo_title ?: $category->name,
            'metaDescription' => $category->seo_description ?: ($category->description ?: 'Photo session category page.'),
        ]);
    }
}
