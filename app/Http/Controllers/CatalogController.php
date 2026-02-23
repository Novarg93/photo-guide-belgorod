<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\PageSeo;
use App\Models\Photo;
use App\Support\CategoryFilterSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function welcome(): Response
    {
        $seo = $this->resolvePageSeo(
            PageSeo::PAGE_WELCOME,
            'Photo sessions in Belgorod',
            'Choose a photo session category in Belgorod and continue to the next planning step.',
        );

        return Inertia::render('Welcome', [
            'metaTitle' => $seo['metaTitle'],
            'metaDescription' => $seo['metaDescription'],
        ]);
    }

    public function index(): Response
    {
        $seo = $this->resolvePageSeo(
            PageSeo::PAGE_CATALOG,
            'Photo Session Catalog',
            'Choose a category and continue to the next planning step.',
        );

        $categories = Category::query()
            ->select(['id', 'name', 'title', 'slug', 'description', 'filter_groups'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'title' => $category->title,
                'slug' => $category->slug,
                'description' => $category->description,
                'filter_groups' => CategoryFilterSchema::normalize($category->filter_groups),
                'url' => route('categories.show', ['slug' => $category->slug]),
            ]);

        return Inertia::render('Catalog', [
            'categories' => $categories,
            'metaTitle' => $seo['metaTitle'],
            'metaDescription' => $seo['metaDescription'],
        ]);
    }

    public function copyright(): Response
    {
        return Inertia::render('Copyright', [
            'metaTitle' => 'Copyright and Photo Sources',
            'metaDescription' => 'Information about photo sources and content removal requests.',
        ]);
    }

    public function locations(): Response
    {
        $seo = $this->resolvePageSeo(
            PageSeo::PAGE_LOCATIONS,
            'Locations Catalog',
            'All available photo locations in Belgorod.',
        );

        $locations = Location::query()
            ->with('category')
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('name')
            ->get()
            ->map(fn (Location $location): array => [
                'id' => $location->id,
                'name' => $location->name,
                'slug' => $location->slug,
                'category' => $location->category?->name,
                'image_url' => $location->image_url,
                'description' => $location->description,
                'url' => route('locations.show', ['slug' => $location->slug]),
            ]);

        return Inertia::render('Locations', [
            'locations' => $locations,
            'metaTitle' => $seo['metaTitle'],
            'metaDescription' => $seo['metaDescription'],
        ]);
    }

    public function locationShow(string $slug): Response
    {
        $location = Location::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with('category:id,name,slug')
            ->firstOrFail();

        return Inertia::render('LocationShow', [
            'location' => [
                'name' => $location->name,
                'slug' => $location->slug,
                'description' => $location->description,
                'image_url' => $location->image_url,
                'category' => $location->category?->name,
                'category_slug' => $location->category?->slug,
                'example_photos' => $location->example_photo_urls,
                'url' => route('locations.show', ['slug' => $location->slug]),
            ],
            'metaTitle' => $location->seo_title ?: "{$location->name} - photo sessions in Belgorod",
            'metaDescription' => $location->seo_description
                ?: ($location->description ?: "Location {$location->name} for photo sessions in Belgorod."),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $filterGroups = CategoryFilterSchema::normalize($category->filter_groups);
        $filterLabelsByKey = CategoryFilterSchema::flattenOptions($category->filter_groups);
        $presets = $category->examples()
            ->where('is_active', true)
            ->select(['id', 'title', 'slug', 'summary', 'filter_option_keys'])
            ->orderBy('title')
            ->get();

        $presetSlug = $request->filled('preset') ? (string) $request->query('preset') : null;
        $requestedFilterOptionKeys = CategoryFilterSchema::filterSelected(
            $category->filter_groups,
            $request->query('filters', []),
        );
        $hasExplicitFilters = $request->has('filters');

        $activePreset = $presetSlug !== null ? $presets->firstWhere('slug', $presetSlug) : null;
        $activeFilterOptionKeys = $requestedFilterOptionKeys;

        if ($activePreset !== null && ! $hasExplicitFilters) {
            $activeFilterOptionKeys = CategoryFilterSchema::filterSelected(
                $category->filter_groups,
                $activePreset->filter_option_keys,
            );
        }

        if ($activePreset !== null && $hasExplicitFilters) {
            $presetFilterOptionKeys = CategoryFilterSchema::filterSelected(
                $category->filter_groups,
                $activePreset->filter_option_keys,
            );

            sort($presetFilterOptionKeys);
            $explicitFilterOptionKeys = $activeFilterOptionKeys;
            sort($explicitFilterOptionKeys);

            if ($presetFilterOptionKeys !== $explicitFilterOptionKeys) {
                $activePreset = null;
            }
        }

        $exampleCards = $category->examples()
            ->where('is_active', true)
            ->whereHas('latestActivePhoto')
            ->with([
                'latestActivePhoto',
                'photos' => fn ($query) => $query
                    ->select(['id', 'example_id', 'filter_option_keys'])
                    ->where('is_active', true),
            ])
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
            ->when(
                $activeFilterOptionKeys !== [],
                function (Builder $query) use ($activeFilterOptionKeys): void {
                    $query->whereHas('photos', function (Builder $photoQuery) use ($activeFilterOptionKeys): void {
                        $photoQuery->where('is_active', true);

                        foreach ($activeFilterOptionKeys as $optionKey) {
                            $photoQuery->whereJsonContains('filter_option_keys', $optionKey);
                        }
                    });
                },
            )
            ->orderBy('title')
            ->get()
            ->map(fn ($example): array => [
                'id' => "example-{$example->id}",
                'example_id' => $example->id,
                'photo_id' => null,
                'title' => $example->title,
                'summary' => $example->summary,
                'mood' => $example->mood,
                'location_hint' => $example->location_hint,
                'season_hint' => $example->season_hint,
                'clothing_hint' => $example->clothing_hint,
                'filter_option_labels' => $example->photos
                    ->flatMap(fn ($photo): array => CategoryFilterSchema::filterSelected($category->filter_groups, $photo->filter_option_keys))
                    ->unique()
                    ->values()
                    ->map(fn (string $optionKey): string => $filterLabelsByKey[$optionKey] ?? $optionKey)
                    ->all(),
                'image_url' => $example->cover_url ?? $example->image_url,
            ]);

        $locations = $category->locations()
            ->where('is_active', true)
            ->select([
                'id',
                'name',
                'slug',
                'photo_path',
                'filter_option_keys',
            ])
            ->when(
                $activeFilterOptionKeys !== [],
                function (Builder $query) use ($activeFilterOptionKeys): void {
                    foreach ($activeFilterOptionKeys as $optionKey) {
                        $query->whereJsonContains('filter_option_keys', $optionKey);
                    }
                },
            )
            ->orderBy('name')
            ->get()
            ->map(fn (Location $location): array => [
                'id' => $location->id,
                'name' => $location->name,
                'slug' => $location->slug,
                'image_url' => $location->image_url,
                'url' => filled($location->slug)
                    ? route('locations.show', ['slug' => $location->slug])
                    : null,
                'filter_option_labels' => collect(
                    CategoryFilterSchema::filterSelected($category->filter_groups, $location->filter_option_keys),
                )
                    ->map(fn (string $optionKey): string => $filterLabelsByKey[$optionKey] ?? $optionKey)
                    ->values()
                    ->all(),
            ]);

        $standalonePhotoCards = $category->photos()
            ->where('is_active', true)
            ->whereNull('example_id')
            ->whereNotNull('path')
            ->select([
                'id',
                'title',
                'path',
                'filter_option_keys',
            ])
            ->when(
                $activeFilterOptionKeys !== [],
                function (Builder $query) use ($activeFilterOptionKeys): void {
                    foreach ($activeFilterOptionKeys as $optionKey) {
                        $query->whereJsonContains('filter_option_keys', $optionKey);
                    }
                },
            )
            ->orderByDesc('id')
            ->get()
            ->map(fn (Photo $photo): array => [
                'id' => "photo-{$photo->id}",
                'example_id' => null,
                'photo_id' => $photo->id,
                'title' => $photo->title,
                'summary' => null,
                'mood' => null,
                'location_hint' => null,
                'season_hint' => null,
                'clothing_hint' => null,
                'image_url' => $photo->url,
                'filter_option_labels' => collect(
                    CategoryFilterSchema::filterSelected($category->filter_groups, $photo->filter_option_keys),
                )
                    ->map(fn (string $optionKey): string => $filterLabelsByKey[$optionKey] ?? $optionKey)
                    ->values()
                    ->all(),
            ]);

        $examples = $exampleCards
            ->concat($standalonePhotoCards)
            ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return Inertia::render('CategoryShow', [
            'category' => [
                'name' => $category->name,
                'title' => $category->title,
                'slug' => $category->slug,
                'description' => $category->description,
            ],
            'examples' => $examples,
            'locations' => $locations,
            'presets' => $presets->map(fn ($preset): array => [
                'id' => $preset->id,
                'title' => $preset->title,
                'slug' => $preset->slug,
                'summary' => $preset->summary,
                'filter_option_keys' => CategoryFilterSchema::filterSelected(
                    $category->filter_groups,
                    $preset->filter_option_keys,
                ),
            ]),
            'activePreset' => $activePreset !== null
                ? [
                    'slug' => $activePreset->slug,
                    'title' => $activePreset->title,
                    'summary' => $activePreset->summary,
                ]
                : null,
            'filterGroups' => $filterGroups,
            'activeFilterOptionKeys' => $activeFilterOptionKeys,
            'metaTitle' => $category->seo_title ?: $category->name,
            'metaDescription' => $category->seo_description ?: ($category->description ?: 'Photo session category page.'),
        ]);
    }

    /**
     * @return array{metaTitle: string, metaDescription: string}
     */
    private function resolvePageSeo(
        string $pageKey,
        string $defaultTitle,
        string $defaultDescription,
    ): array {
        $seo = PageSeo::query()
            ->where('page_key', $pageKey)
            ->first();

        return [
            'metaTitle' => $seo?->seo_title ?: $defaultTitle,
            'metaDescription' => $seo?->seo_description ?: $defaultDescription,
        ];
    }
}
