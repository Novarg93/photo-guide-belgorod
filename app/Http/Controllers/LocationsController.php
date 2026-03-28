<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\PageSeo;
use Inertia\Inertia;
use Inertia\Response;

class LocationsController extends Controller
{
    public function index(): Response
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

    public function show(string $slug): Response
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
