<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Best Time for Outdoor Photos in Belgorod',
                'excerpt' => 'How to pick the right season and light for your outdoor photo session.',
            ],
            [
                'title' => 'What to Wear for a Family Photo Session',
                'excerpt' => 'Simple clothing combinations that look balanced on camera.',
            ],
            [
                'title' => 'Top 7 Mistakes Before a Photo Shoot',
                'excerpt' => 'Avoid common mistakes that make the session stressful.',
            ],
            [
                'title' => 'How to Choose a Location for Wedding Photos',
                'excerpt' => 'A practical checklist to pick a wedding location quickly.',
            ],
            [
                'title' => 'Studio vs Outdoor Session: What to Choose',
                'excerpt' => 'Pros and cons of studio and outdoor formats.',
            ],
            [
                'title' => 'Posing Tips for Couples Who Feel Shy',
                'excerpt' => 'Natural posing ideas without stiff or awkward shots.',
            ],
            [
                'title' => 'Photo Session Budget Planning Guide',
                'excerpt' => 'How to build a realistic budget without hidden surprises.',
            ],
            [
                'title' => 'How to Prepare Kids for a Family Shoot',
                'excerpt' => 'Preparation tips that help children stay engaged and relaxed.',
            ],
            [
                'title' => 'Golden Hour in Belgorod: Practical Guide',
                'excerpt' => 'Why golden hour works and how to schedule your session around it.',
            ],
            [
                'title' => 'Checklist Before You Book a Photographer',
                'excerpt' => 'Questions to ask and details to confirm before booking.',
            ],
        ];

        foreach ($blogs as $index => $blogData) {
            Blog::query()->updateOrCreate(
                ['title' => $blogData['title']],
                [
                    'slug' => Str::slug($blogData['title']),
                    'cover_image' => 'photos/placeholder.svg',
                    'excerpt' => $blogData['excerpt'],
                    'content' => $blogData['excerpt']."\n\n".'This article contains practical recommendations and examples for planning a better photo session in Belgorod.',
                    'seo_title' => $blogData['title'].' | Photo Guide Belgorod',
                    'seo_description' => $blogData['excerpt'],
                    'published_at' => now()->subDays(10 - $index),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
