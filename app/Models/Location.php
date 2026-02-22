<?php

namespace App\Models;

use App\Support\CategoryFilterSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'photo_path',
        'filter_option_keys',
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
        static::saving(function (Location $location): void {
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
}
