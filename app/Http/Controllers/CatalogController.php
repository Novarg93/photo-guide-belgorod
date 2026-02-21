<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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

    public function show(Request $request, string $slug): Response
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $activeFilters = [
            'mood' => $request->filled('mood') ? (string) $request->query('mood') : null,
            'season' => $request->filled('season') ? (string) $request->query('season') : null,
            'location' => $request->filled('location') ? (string) $request->query('location') : null,
            'clothing' => $request->filled('clothing') ? (string) $request->query('clothing') : null,
        ];

        $activeExamplesQuery = $category->examples()
            ->where('is_active', true);

        $filterOptions = [
            'moods' => (clone $activeExamplesQuery)
                ->whereNotNull('mood')
                ->distinct()
                ->orderBy('mood')
                ->pluck('mood')
                ->values()
                ->all(),
            'seasons' => (clone $activeExamplesQuery)
                ->whereNotNull('season_hint')
                ->distinct()
                ->orderBy('season_hint')
                ->pluck('season_hint')
                ->values()
                ->all(),
            'locations' => (clone $activeExamplesQuery)
                ->whereNotNull('location_hint')
                ->distinct()
                ->orderBy('location_hint')
                ->pluck('location_hint')
                ->values()
                ->all(),
            'clothings' => (clone $activeExamplesQuery)
                ->whereNotNull('clothing_hint')
                ->distinct()
                ->orderBy('clothing_hint')
                ->pluck('clothing_hint')
                ->values()
                ->all(),
        ];

        $examples = $activeExamplesQuery
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
            ->when($activeFilters['mood'], fn (Builder $query, string $value) => $query->where('mood', $value))
            ->when($activeFilters['season'], fn (Builder $query, string $value) => $query->where('season_hint', $value))
            ->when($activeFilters['location'], fn (Builder $query, string $value) => $query->where('location_hint', $value))
            ->when($activeFilters['clothing'], fn (Builder $query, string $value) => $query->where('clothing_hint', $value))
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
                'slug' => $category->slug,
                'description' => $category->description,
            ],
            'examples' => $examples,
            'filterOptions' => $filterOptions,
            'activeFilters' => $activeFilters,
            'metaTitle' => $category->seo_title ?: $category->name,
            'metaDescription' => $category->seo_description ?: ($category->description ?: 'Photo session category page.'),
        ]);
    }
}
