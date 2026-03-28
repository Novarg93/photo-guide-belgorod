<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Photographer extends Model
{
    /** @use HasFactory<\Database\Factories\PhotographerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'url',
        'image_path',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Photographer $photographer): void {
            if (! $photographer->exists && blank($photographer->slug)) {
                $photographer->slug = static::generateUniqueSlug($photographer->name);

                return;
            }

            if ($photographer->exists && $photographer->isDirty('name') && ! $photographer->isDirty('slug')) {
                $photographer->slug = static::generateUniqueSlug($photographer->name, $photographer->getKey());
            }
        });
    }

    public function getImageUrlAttribute(): string
    {
        if (filled($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return Storage::disk('public')->url('photos/placeholder.svg');
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'photographer';
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
