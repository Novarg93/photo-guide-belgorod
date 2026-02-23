<?php

namespace App\Models;

use App\Support\CategoryFilterSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'photo_path',
        'example_photo_paths',
        'filter_option_keys',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'filter_option_keys' => 'array',
            'example_photo_paths' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Location $location): void {
            if (! $location->exists && blank($location->slug)) {
                $location->slug = static::generateUniqueSlug($location->name);
            }

            if ($location->exists && $location->isDirty('name') && ! $location->isDirty('slug')) {
                $location->slug = static::generateUniqueSlug($location->name, $location->getKey());
            }

            $categoryFilterGroups = Category::query()
                ->whereKey($location->category_id)
                ->value('filter_groups');

            $location->filter_option_keys = CategoryFilterSchema::filterSelected(
                $categoryFilterGroups,
                $location->filter_option_keys,
            );
        });
    }

    public function getImageUrlAttribute(): string
    {
        if (filled($this->photo_path)) {
            return Storage::disk('public')->url($this->photo_path);
        }

        return Storage::disk('public')->url('photos/placeholder.svg');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getExamplePhotoUrlsAttribute(): array
    {
        return collect($this->example_photo_paths ?? [])
            ->filter(fn (mixed $path): bool => filled($path))
            ->map(fn (string $path): string => Storage::disk('public')->url($path))
            ->values()
            ->all();
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'location';
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
