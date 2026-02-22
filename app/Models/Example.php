<?php

namespace App\Models;

use App\Support\CategoryFilterSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Example extends Model
{
    /** @use HasFactory<\Database\Factories\ExampleFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'summary',
        'filter_option_keys',
        'mood',
        'location_hint',
        'season_hint',
        'clothing_hint',
        'cover_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'filter_option_keys' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getImageUrlAttribute(): string
    {
        if (filled($this->cover_image)) {
            return $this->cover_image;
        }

        return Storage::disk('public')->url('photos/placeholder.svg');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $photo = $this->relationLoaded('latestActivePhoto')
            ? $this->getRelation('latestActivePhoto')
            : $this->latestActivePhoto()->first();

        return $photo?->url;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function latestActivePhoto(): HasOne
    {
        return $this->hasOne(Photo::class)
            ->where('is_active', true)
            ->whereNotNull('path')
            ->latestOfMany();
    }

    protected static function booted(): void
    {
        static::saving(function (Example $example): void {
            if (! $example->exists && blank($example->slug)) {
                $example->slug = static::generateUniqueSlug($example->title);

                return;
            }

            if ($example->exists && $example->isDirty('title') && ! $example->isDirty('slug')) {
                $example->slug = static::generateUniqueSlug($example->title, $example->getKey());
            }

            $categoryFilterGroups = Category::query()
                ->whereKey($example->category_id)
                ->first()?->filter_groups;

            $example->filter_option_keys = CategoryFilterSchema::filterSelected(
                $categoryFilterGroups,
                $example->filter_option_keys,
            );
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'example';
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
