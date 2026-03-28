<?php

namespace App\Http\Controllers;

use App\Models\Photographer;
use Inertia\Inertia;
use Inertia\Response;

class PhotographersController extends Controller
{
    public function index(): Response
    {
        $photographers = Photographer::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('name')
            ->get()
            ->map(fn (Photographer $photographer): array => [
                'id' => $photographer->id,
                'name' => $photographer->name,
                'slug' => $photographer->slug,
                'profile_url' => $photographer->url,
                'image_url' => $photographer->image_url,
                'description' => $photographer->description,
                'url' => route('photographers.show', ['slug' => $photographer->slug]),
            ]);

        return Inertia::render('Photographers', [
            'photographers' => $photographers,
            'metaTitle' => 'Photographers Catalog',
            'metaDescription' => 'Browse photographers in Belgorod and check their portfolios.',
        ]);
    }

    public function show(string $slug): Response
    {
        $photographer = Photographer::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('PhotographerShow', [
            'photographer' => [
                'id' => $photographer->id,
                'name' => $photographer->name,
                'slug' => $photographer->slug,
                'profile_url' => $photographer->url,
                'image_url' => $photographer->image_url,
                'description' => $photographer->description,
                'url' => route('photographers.show', ['slug' => $photographer->slug]),
            ],
            'metaTitle' => "{$photographer->name} - photographer in Belgorod",
            'metaDescription' => $photographer->description ?: "Photographer {$photographer->name} in Belgorod.",
        ]);
    }
}
