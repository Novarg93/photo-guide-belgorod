<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Wedding',
                'description' => 'Wedding photo sessions in Belgorod.',
                'filter_groups' => [
                    [
                        'name' => 'Mood',
                        'options' => [
                            ['name' => 'Elegant'],
                            ['name' => 'Romantic'],
                            ['name' => 'Documentary'],
                        ],
                    ],
                    [
                        'name' => 'Location Type',
                        'options' => [
                            ['name' => 'Park'],
                            ['name' => 'Studio'],
                            ['name' => 'City'],
                        ],
                    ],
                    [
                        'name' => 'Color Style',
                        'options' => [
                            ['name' => 'Warm'],
                            ['name' => 'Neutral'],
                            ['name' => 'Film'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Family',
                'description' => 'Family photo sessions in Belgorod.',
                'filter_groups' => [
                    [
                        'name' => 'Mood',
                        'options' => [
                            ['name' => 'Calm'],
                            ['name' => 'Playful'],
                            ['name' => 'Cozy'],
                        ],
                    ],
                    [
                        'name' => 'Season',
                        'options' => [
                            ['name' => 'Spring'],
                            ['name' => 'Summer'],
                            ['name' => 'Autumn'],
                            ['name' => 'Winter'],
                        ],
                    ],
                    [
                        'name' => 'Clothing',
                        'options' => [
                            ['name' => 'Casual'],
                            ['name' => 'Classic'],
                            ['name' => 'Minimal'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Love Story',
                'description' => 'Couple and love story sessions in Belgorod.',
                'filter_groups' => [
                    [
                        'name' => 'Mood',
                        'options' => [
                            ['name' => 'Tender'],
                            ['name' => 'Bold'],
                            ['name' => 'Cinematic'],
                        ],
                    ],
                    [
                        'name' => 'Time',
                        'options' => [
                            ['name' => 'Morning'],
                            ['name' => 'Golden Hour'],
                            ['name' => 'Night City'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Personal',
                'description' => 'Personal branding and portrait sessions.',
                'filter_groups' => [
                    [
                        'name' => 'Style',
                        'options' => [
                            ['name' => 'Business'],
                            ['name' => 'Creative'],
                            ['name' => 'Street'],
                        ],
                    ],
                    [
                        'name' => 'Light',
                        'options' => [
                            ['name' => 'Soft'],
                            ['name' => 'Contrast'],
                            ['name' => 'Natural'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Social Content',
                'description' => 'Visual packs for social media content.',
                'filter_groups' => [
                    [
                        'name' => 'Format',
                        'options' => [
                            ['name' => 'Lifestyle'],
                            ['name' => 'Product'],
                            ['name' => 'Behind the Scenes'],
                        ],
                    ],
                    [
                        'name' => 'Tone',
                        'options' => [
                            ['name' => 'Bright'],
                            ['name' => 'Muted'],
                            ['name' => 'High Contrast'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Pregnancy',
                'description' => 'Pregnancy and maternity sessions.',
                'filter_groups' => [
                    [
                        'name' => 'Mood',
                        'options' => [
                            ['name' => 'Tender'],
                            ['name' => 'Minimal'],
                            ['name' => 'Fine Art'],
                        ],
                    ],
                    [
                        'name' => 'Location',
                        'options' => [
                            ['name' => 'Studio'],
                            ['name' => 'Home'],
                            ['name' => 'Outdoor'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Kids',
                'description' => 'Kids and children sessions.',
                'filter_groups' => [
                    [
                        'name' => 'Mood',
                        'options' => [
                            ['name' => 'Energetic'],
                            ['name' => 'Natural'],
                            ['name' => 'Storytelling'],
                        ],
                    ],
                    [
                        'name' => 'Location Type',
                        'options' => [
                            ['name' => 'Playground'],
                            ['name' => 'Park'],
                            ['name' => 'Studio'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'filter_groups' => $categoryData['filter_groups'],
                    'is_active' => true,
                ],
            );
        }
    }
}
