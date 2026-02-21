<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brief extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'public_token',
        'filters',
        'selected_example_ids',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'selected_example_ids' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
