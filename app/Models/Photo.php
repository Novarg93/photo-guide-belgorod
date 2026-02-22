<?php

namespace App\Models;

use App\Support\CategoryFilterSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Photo extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'filter_option_keys',
        'example_id',
        'path',
        'source_type',
        'source_url',
        'author_name',
        'license',
        'permission_note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'filter_option_keys' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Photo $photo): void {
            if (blank($photo->title) && filled($photo->path)) {
                $photo->title = pathinfo((string) $photo->path, PATHINFO_FILENAME);
            }

            if (blank($photo->title)) {
                $photo->title = 'photo';
            }

            if (filled($photo->example_id)) {
                $exampleCategoryId = Example::query()
                    ->whereKey($photo->example_id)
                    ->value('category_id');

                if ($exampleCategoryId !== null) {
                    $photo->category_id = $exampleCategoryId;
                }
            }

            $categoryFilterGroups = Category::query()
                ->whereKey($photo->category_id)
                ->first()?->filter_groups;

            $photo->filter_option_keys = CategoryFilterSchema::filterSelected(
                $categoryFilterGroups,
                $photo->filter_option_keys,
            );

            $photo->title = Str::limit(trim((string) $photo->title), 255, '');
        });
    }

    public function getUrlAttribute(): ?string
    {
        if (blank($this->path)) {
            return null;
        }

        return Storage::disk('public')->url($this->path);
    }

    public function example(): BelongsTo
    {
        return $this->belongsTo(Example::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
