<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $blogs = Blog::query()
            ->select(['id', 'title', 'slug', 'cover_image', 'excerpt', 'published_at'])
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Blog $blog): array => [
                'id' => $blog->id,
                'title' => $blog->title,
                'excerpt' => $blog->excerpt,
                'image_url' => $blog->cover_url,
                'published_at' => $blog->published_at?->toDateString(),
                'url' => route('blogs.show', ['slug' => $blog->slug]),
            ]);

        return Inertia::render('Blogs', [
            'blogs' => $blogs,
            'metaTitle' => 'Blog',
            'metaDescription' => 'Tips and guides for better photo sessions in Belgorod.',
        ]);
    }

    public function show(string $slug): Response
    {
        $blog = Blog::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('BlogShow', [
            'blog' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt' => $blog->excerpt,
                'content' => $blog->content,
                'image_url' => $blog->cover_url,
                'published_at' => $blog->published_at?->toDateString(),
                'url' => route('blogs.show', ['slug' => $blog->slug]),
            ],
            'metaTitle' => $blog->seo_title ?: $blog->title,
            'metaDescription' => $blog->seo_description ?: ($blog->excerpt ?: 'Blog article from Photo Guide Belgorod.'),
        ]);
    }
}
