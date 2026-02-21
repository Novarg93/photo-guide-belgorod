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

    public function copyright(): Response
    {
        return Inertia::render('Copyright', [
            'metaTitle' => 'Copyright and Photo Sources',
            'metaDescription' => 'Information about photo sources and content removal requests.',
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $presets = $category->examples()
            ->select([
                'id',
                'title',
                'slug',
                'summary',
                'mood',
                'season_hint',
                'location_hint',
                'clothing_hint',
            ])
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        $activePreset = null;
        $presetSlug = $request->filled('preset') ? (string) $request->query('preset') : null;

        if ($presetSlug !== null) {
            $activePreset = $presets->firstWhere('slug', $presetSlug);
        }

        $queryFilters = [
            'mood' => $request->filled('mood') ? (string) $request->query('mood') : null,
            'season' => $request->filled('season') ? (string) $request->query('season') : null,
            'location' => $request->filled('location') ? (string) $request->query('location') : null,
            'clothing' => $request->filled('clothing') ? (string) $request->query('clothing') : null,
        ];

        $explicitFilters = [
            'mood' => $request->filled('mood'),
            'season' => $request->filled('season'),
            'location' => $request->filled('location'),
            'clothing' => $request->filled('clothing'),
        ];

        $activeFilters = $queryFilters;

        if ($activePreset !== null) {
            if (! $explicitFilters['mood']) {
                $activeFilters['mood'] = $activePreset->mood;
            }

            if (! $explicitFilters['season']) {
                $activeFilters['season'] = $activePreset->season_hint;
            }

            if (! $explicitFilters['location']) {
                $activeFilters['location'] = $activePreset->location_hint;
            }

            if (! $explicitFilters['clothing']) {
                $activeFilters['clothing'] = $activePreset->clothing_hint;
            }
        }

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
            ->with(['latestActivePhoto'])
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
                'image_url' => $example->cover_url ?? $example->image_url,
            ]);

        return Inertia::render('CategoryShow', [
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ],
            'examples' => $examples,
            'presets' => $presets->map(fn ($preset): array => [
                'id' => $preset->id,
                'title' => $preset->title,
                'slug' => $preset->slug,
                'summary' => $preset->summary,
                'mood' => $preset->mood,
                'season_hint' => $preset->season_hint,
                'location_hint' => $preset->location_hint,
                'clothing_hint' => $preset->clothing_hint,
            ]),
            'activePreset' => $activePreset !== null
                ? [
                    'slug' => $activePreset->slug,
                    'title' => $activePreset->title,
                    'summary' => $activePreset->summary,
                ]
                : null,
            'filterOptions' => $filterOptions,
            'activeFilters' => $activeFilters,
            'metaTitle' => $category->seo_title ?: $category->name,
            'metaDescription' => $category->seo_description ?: ($category->description ?: 'Photo session category page.'),
        ]);
    }
}
