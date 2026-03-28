<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends Controller
{
    public function show(string $slug): Response
    {
        $page = LegalPage::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('LegalPageShow', [
            'page' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'excerpt' => $page->excerpt,
                'content' => $page->content,
            ],
            'metaTitle' => $page->seo_title ?: $page->title,
            'metaDescription' => $page->seo_description ?: ($page->excerpt ?: 'Legal information page.'),
        ]);
    }

    public function copyright(): Response
    {
        return Inertia::render('Copyright', [
            'metaTitle' => 'Copyright and Photo Sources',
            'metaDescription' => 'Information about photo sources and content removal requests.',
        ]);
    }
}
