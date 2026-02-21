<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBriefRequest;
use App\Models\Brief;
use App\Models\Category;
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
        ];

        $selectedExampleIds = $category->examples()
            ->where('is_active', true)
            ->whereIn('id', $validated['selected_example_ids'] ?? [])
            ->pluck('id')
            ->values()
            ->all();

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
        ];

        $selectedExampleIds = collect($brief->selected_example_ids ?? [])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($selectedExampleIds->isNotEmpty()) {
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
        } else {
            $examplesCollection = $brief->category->examples()
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
                ->when($filters['mood'], fn (Builder $query, string $value) => $query->where('mood', $value))
                ->when($filters['season'], fn (Builder $query, string $value) => $query->where('season_hint', $value))
                ->when($filters['location'], fn (Builder $query, string $value) => $query->where('location_hint', $value))
                ->when($filters['clothing'], fn (Builder $query, string $value) => $query->where('clothing_hint', $value))
                ->orderBy('title')
                ->limit(6)
                ->get();
        }

        $examples = $examplesCollection
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

        return Inertia::render('BriefShow', [
            'category' => [
                'name' => $brief->category->name,
                'slug' => $brief->category->slug,
                'description' => $brief->category->description,
            ],
            'filters' => $filters,
            'examples' => $examples,
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
