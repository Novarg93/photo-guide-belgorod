<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Blog extends Model
{
    /** @use HasFactory<\Database\Factories\BlogFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'cover_image',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
        'published_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Blog $blog): void {
            if (! $blog->exists && blank($blog->slug)) {
                $blog->slug = static::generateUniqueSlug($blog->title);

                return;
            }

            if ($blog->exists && $blog->isDirty('title') && ! $blog->isDirty('slug')) {
                $blog->slug = static::generateUniqueSlug($blog->title, $blog->getKey());
            }
        });
    }

    public function getCoverUrlAttribute(): string
    {
        if (filled($this->cover_image)) {
            return Storage::disk('public')->url($this->cover_image);
        }

        return Storage::disk('public')->url('photos/placeholder.svg');
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'blog';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            static::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
