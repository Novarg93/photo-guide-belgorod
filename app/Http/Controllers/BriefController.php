<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBriefRequest;
use App\Models\Brief;
use App\Models\Category;
use App\Models\Photo;
use App\Support\CategoryFilterSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BriefController extends Controller
{
    public function store(StoreBriefRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $category = Category::query()
            ->where('slug', $validated['category_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        $filters = [
            'mood' => $validated['mood'] ?? null,
            'season' => $validated['season'] ?? null,
            'location' => $validated['location'] ?? null,
            'clothing' => $validated['clothing'] ?? null,
            'active_filter_option_keys' => CategoryFilterSchema::filterSelected(
                $category->filter_groups,
                $validated['active_filter_option_keys'] ?? [],
            ),
        ];

        $selectedExampleIds = $category->examples()
            ->where('is_active', true)
            ->whereIn('id', $validated['selected_example_ids'] ?? [])
            ->pluck('id')
            ->values()
            ->all();

        $selectedPhotoIds = $category->photos()
            ->where('is_active', true)
            ->whereNull('example_id')
            ->whereIn('id', $validated['selected_photo_ids'] ?? [])
            ->pluck('id')
            ->values()
            ->all();

        $filters['selected_photo_ids'] = $selectedPhotoIds;

        $brief = Brief::query()->create([
            'category_id' => $category->id,
            'public_token' => (string) Str::uuid(),
            'filters' => $filters,
            'selected_example_ids' => $selectedExampleIds,
            'people_count' => $validated['people_count'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'retouch_preference' => $validated['retouch_preference'] ?? null,
            'color_style' => $validated['color_style'] ?? null,
        ]);

        return redirect()->route('brief.show', ['token' => $brief->public_token]);
    }

    public function show(string $token): Response
    {
        $brief = Brief::query()
            ->with('category')
            ->where('public_token', $token)
            ->firstOrFail();

        $filters = [
            'mood' => $brief->filters['mood'] ?? null,
            'season' => $brief->filters['season'] ?? null,
            'location' => $brief->filters['location'] ?? null,
            'clothing' => $brief->filters['clothing'] ?? null,
            'active_filter_option_keys' => $brief->filters['active_filter_option_keys'] ?? [],
        ];
        $activeFilterOptionKeys = CategoryFilterSchema::filterSelected(
            $brief->category->filter_groups,
            $brief->filters['active_filter_option_keys'] ?? [],
        );

        $selectedExampleIds = collect($brief->selected_example_ids ?? [])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();
        $selectedPhotoIds = collect($brief->filters['selected_photo_ids'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();
        $selectedCardFilterOptionKeys = collect();

        if ($selectedExampleIds->isNotEmpty()) {
            $selectedExampleFilterOptionKeys = $brief->category->photos()
                ->select(['id', 'filter_option_keys'])
                ->where('is_active', true)
                ->whereIn('example_id', $selectedExampleIds)
                ->get()
                ->flatMap(fn (Photo $photo): array => CategoryFilterSchema::filterSelected(
                    $brief->category->filter_groups,
                    $photo->filter_option_keys,
                ));

            $selectedCardFilterOptionKeys = $selectedCardFilterOptionKeys->concat($selectedExampleFilterOptionKeys);
        }

        if ($selectedPhotoIds->isNotEmpty()) {
            $selectedPhotoFilterOptionKeys = $brief->category->photos()
                ->select(['id', 'filter_option_keys'])
                ->where('is_active', true)
                ->whereNull('example_id')
                ->whereIn('id', $selectedPhotoIds)
                ->get()
                ->flatMap(fn (Photo $photo): array => CategoryFilterSchema::filterSelected(
                    $brief->category->filter_groups,
                    $photo->filter_option_keys,
                ));

            $selectedCardFilterOptionKeys = $selectedCardFilterOptionKeys->concat($selectedPhotoFilterOptionKeys);
        }

        $effectiveLocationFilterOptionKeys = collect($activeFilterOptionKeys)
            ->when(
                $activeFilterOptionKeys === [],
                fn ($collection) => $collection->concat($selectedCardFilterOptionKeys),
            )
            ->unique()
            ->values()
            ->all();

        if ($selectedExampleIds->isNotEmpty() || $selectedPhotoIds->isNotEmpty()) {
            $selectedExamplesById = $brief->category->examples()
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
                ->whereIn('id', $selectedExampleIds)
                ->get()
                ->keyBy('id');

            $examplesCollection = $selectedExampleIds
                ->map(fn (int $id) => $selectedExamplesById->get($id))
                ->filter();

            $selectedPhotosById = $brief->category->photos()
                ->select(['id', 'title', 'path'])
                ->where('is_active', true)
                ->whereNull('example_id')
                ->whereIn('id', $selectedPhotoIds)
                ->get()
                ->keyBy('id');

            $selectedPhotosCollection = $selectedPhotoIds
                ->map(fn (int $id): ?Photo => $selectedPhotosById->get($id))
                ->filter();

            $examples = $examplesCollection
                ->map(fn ($example): array => [
                    'id' => "example-{$example->id}",
                    'title' => $example->title,
                    'summary' => $example->summary,
                    'mood' => $example->mood,
                    'location_hint' => $example->location_hint,
                    'season_hint' => $example->season_hint,
                    'clothing_hint' => $example->clothing_hint,
                    'image_url' => $example->image_url,
                ])
                ->concat(
                    $selectedPhotosCollection->map(fn (Photo $photo): array => [
                        'id' => "photo-{$photo->id}",
                        'title' => $photo->title,
                        'summary' => null,
                        'mood' => null,
                        'location_hint' => null,
                        'season_hint' => null,
                        'clothing_hint' => null,
                        'image_url' => $photo->url,
                    ]),
                )
                ->values();
        } else {
            $exampleCards = $brief->category->examples()
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
                ->with('latestActivePhoto')
                ->whereHas('latestActivePhoto')
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
                ->when($filters['mood'], fn (Builder $query, string $value) => $query->where('mood', $value))
                ->when($filters['season'], fn (Builder $query, string $value) => $query->where('season_hint', $value))
                ->when($filters['location'], fn (Builder $query, string $value) => $query->where('location_hint', $value))
                ->when($filters['clothing'], fn (Builder $query, string $value) => $query->where('clothing_hint', $value))
                ->orderBy('title')
                ->get()
                ->map(fn ($example): array => [
                    'id' => "example-{$example->id}",
                    'title' => $example->title,
                    'summary' => $example->summary,
                    'mood' => $example->mood,
                    'location_hint' => $example->location_hint,
                    'season_hint' => $example->season_hint,
                    'clothing_hint' => $example->clothing_hint,
                    'image_url' => $example->cover_url ?? $example->image_url,
                ]);

            $standalonePhotoCards = $brief->category->photos()
                ->select(['id', 'title', 'path', 'filter_option_keys'])
                ->where('is_active', true)
                ->whereNull('example_id')
                ->whereNotNull('path')
                ->when(
                    $activeFilterOptionKeys !== [],
                    function (Builder $query) use ($activeFilterOptionKeys): void {
                        foreach ($activeFilterOptionKeys as $optionKey) {
                            $query->whereJsonContains('filter_option_keys', $optionKey);
                        }
                    },
                )
                ->orderBy('title')
                ->get()
                ->map(fn (Photo $photo): array => [
                    'id' => "photo-{$photo->id}",
                    'title' => $photo->title,
                    'summary' => null,
                    'mood' => null,
                    'location_hint' => null,
                    'season_hint' => null,
                    'clothing_hint' => null,
                    'image_url' => $photo->url,
                ]);

            $examples = $exampleCards
                ->concat($standalonePhotoCards)
                ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();
        }

        $filterLabelsByKey = CategoryFilterSchema::flattenOptions($brief->category->filter_groups);
        $locationFilterOptionLabels = collect($effectiveLocationFilterOptionKeys)
            ->map(fn (string $optionKey): string => $filterLabelsByKey[$optionKey] ?? $optionKey)
            ->values()
            ->all();

        $locations = $brief->category->locations()
            ->where('is_active', true)
            ->select(['id', 'name', 'photo_path', 'filter_option_keys'])
            ->when(
                $effectiveLocationFilterOptionKeys !== [],
                function (Builder $query) use ($effectiveLocationFilterOptionKeys): void {
                    foreach ($effectiveLocationFilterOptionKeys as $optionKey) {
                        $query->whereJsonContains('filter_option_keys', $optionKey);
                    }
                },
            )
            ->orderBy('name')
            ->get()
            ->map(fn ($location): array => [
                'id' => $location->id,
                'name' => $location->name,
                'image_url' => $location->image_url,
                'filter_option_labels' => collect(
                    CategoryFilterSchema::filterSelected($brief->category->filter_groups, $location->filter_option_keys),
                )
                    ->map(fn (string $optionKey): string => $filterLabelsByKey[$optionKey] ?? $optionKey)
                    ->values()
                    ->all(),
            ]);

        return Inertia::render('BriefShow', [
            'category' => [
                'name' => $brief->category->name,
                'slug' => $brief->category->slug,
                'description' => $brief->category->description,
            ],
            'filters' => $filters,
            'locationFilterOptionLabels' => $locationFilterOptionLabels,
            'examples' => $examples,
            'locations' => $locations,
            'selectedExampleIds' => $selectedExampleIds->all(),
            'peopleCount' => $brief->people_count,
            'notes' => $brief->notes,
            'retouchPreference' => $brief->retouch_preference,
            'colorStyle' => $brief->color_style,
            'token' => $brief->public_token,
            'shareUrl' => route('brief.show', ['token' => $brief->public_token]),
            'metaTitle' => 'Brief: '.$brief->category->name,
            'metaDescription' => 'Share this brief link with your photographer.',
        ]);
    }
}
