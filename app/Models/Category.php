<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function examples(): HasMany
    {
        return $this->hasMany(Example::class);
    }

    public function briefs(): HasMany
    {
        return $this->hasMany(Brief::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (! $category->exists && blank($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);

                return;
            }

            if ($category->exists && $category->isDirty('name') && ! $category->isDirty('slug')) {
                $category->slug = static::generateUniqueSlug($category->name, $category->getKey());
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'category';
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
