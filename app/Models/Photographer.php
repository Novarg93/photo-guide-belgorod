<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photographer extends Model
{
    /** @use HasFactory<\Database\Factories\PhotographerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
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

    public function getImageUrlAttribute(): string
    {
        if (filled($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return Storage::disk('public')->url('photos/placeholder.svg');
    }
}
