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

        return Inertia::render('CategoryShow', [
            'category' => [
                'name' => $category->name,
                'description' => $category->description,
            ],
            'metaTitle' => $category->seo_title ?: $category->name,
            'metaDescription' => $category->seo_description ?: ($category->description ?: 'Photo session category page.'),
        ]);
    }
}
