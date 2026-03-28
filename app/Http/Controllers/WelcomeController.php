<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Location;
use App\Models\PageSeo;
use App\Models\Photographer;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function __invoke(): Response
    {
        $seo = $this->resolvePageSeo(
            PageSeo::PAGE_WELCOME,
            'Photo sessions in Belgorod',
            'Choose a photo session category in Belgorod and continue to the next planning step.',
        );

        $categories = Category::query()
            ->select(['id', 'name', 'title', 'slug', 'description', 'image_path'])
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('name')
            ->limit(4)
            ->get()
            ->map(fn(Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'title' => $category->title,
                'description' => $category->description,
                'image' => $category->image_url,
                'url' => route('categories.show', ['slug' => $category->slug]),
            ]);

        $locations = Location::query()
            ->select(['id', 'name', 'slug', 'description', 'photo_path'])
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('name')
            ->limit(4)
            ->get()
            ->map(fn(Location $location): array => [
                'id' => $location->id,
                'name' => $location->name,
                'description' => $location->description,
                'image_url' => $location->image_url,
                'url' => route('locations.show', ['slug' => $location->slug]),
            ]);

        $photographers = Photographer::query()
            ->select(['id', 'name', 'url', 'image_path', 'description'])
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->map(fn(Photographer $photographer): array => [
                'id' => $photographer->id,
                'name' => $photographer->name,
                'url' => $photographer->url,
                'image_url' => $photographer->image_url,
                'description' => $photographer->description,
            ]);

        $blogs = Blog::query()
            ->select(['id', 'title', 'slug', 'cover_image', 'excerpt', 'published_at'])
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get()
            ->map(fn(Blog $blog): array => [
                'id' => $blog->id,
                'title' => $blog->title,
                'excerpt' => $blog->excerpt,
                'image_url' => $blog->cover_url,
                'published_at' => $blog->published_at?->toDateString(),
                'url' => route('blogs.show', ['slug' => $blog->slug]),
            ]);

        $faqs = Faq::query()
            ->select(['id', 'question', 'answer', 'sort_order'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(Faq $faq): array => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
            ]);

        return Inertia::render('Welcome', [
            'categories' => $categories,
            'locations' => $locations,
            'photographers' => $photographers,
            'blogs' => $blogs,
            'faqs' => $faqs,
            'metaTitle' => $seo['metaTitle'],
            'metaDescription' => $seo['metaDescription'],
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
