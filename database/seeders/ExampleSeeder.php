<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Example;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exampleMatrix = [
            [
                'title' => 'Golden Hour Walk',
                'summary' => 'A soft evening walk with natural city light and relaxed pacing.',
                'mood' => 'Romantic',
                'location_hint' => 'City center streets',
                'season_hint' => 'Summer',
                'clothing_hint' => 'Light neutrals',
            ],
            [
                'title' => 'Minimal Studio Story',
                'summary' => 'Clean studio frames focused on emotion and simple composition.',
                'mood' => 'Editorial',
                'location_hint' => 'Studio loft',
                'season_hint' => 'Winter',
                'clothing_hint' => 'Monochrome layers',
            ],
            [
                'title' => 'Pine Park Session',
                'summary' => 'Natural textures and calm movement under tree shadows.',
                'mood' => 'Natural',
                'location_hint' => 'Pine park',
                'season_hint' => 'Spring',
                'clothing_hint' => 'Casual earth tones',
            ],
            [
                'title' => 'Morning Riverside',
                'summary' => 'Fresh morning portraits with open space and gentle tones.',
                'mood' => 'Calm',
                'location_hint' => 'Riverside',
                'season_hint' => 'Autumn',
                'clothing_hint' => 'Knitwear and denim',
            ],
            [
                'title' => 'Urban Contrast',
                'summary' => 'Bold geometry and direct framing for a modern look.',
                'mood' => 'Bold',
                'location_hint' => 'Concrete district',
                'season_hint' => 'Summer',
                'clothing_hint' => 'Black and white',
            ],
            [
                'title' => 'Cozy Indoor Moments',
                'summary' => 'Warm indoor storytelling with close-up details and texture.',
                'mood' => 'Warm',
                'location_hint' => 'Apartment interior',
                'season_hint' => 'Winter',
                'clothing_hint' => 'Soft knit textures',
            ],
        ];

        Category::query()->select(['id', 'name', 'slug'])->get()->each(function (Category $category) use ($exampleMatrix): void {
            foreach ($exampleMatrix as $index => $example) {
                $title = "{$category->name}: {$example['title']}";

                Example::query()->updateOrCreate(
                    ['slug' => Str::slug("{$category->slug}-example-{$index}")],
                    [
                        'category_id' => $category->id,
                        'title' => $title,
                        'summary' => $example['summary'],
                        'mood' => $example['mood'],
                        'location_hint' => $example['location_hint'],
                        'season_hint' => $example['season_hint'],
                        'clothing_hint' => $example['clothing_hint'],
                        'is_active' => true,
                    ],
                );
            }
        });
    }
}
