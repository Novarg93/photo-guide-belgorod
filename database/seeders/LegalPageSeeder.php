<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'excerpt' => 'How we collect, process, and protect personal information.',
                'content' => "This Privacy Policy explains how Photo Guide Belgorod handles personal data.\n\nWe may collect technical and contact information submitted through forms.\n\nIf you need data removal, contact us via the details listed on the website.",
                'seo_title' => 'Privacy Policy - Photo Guide Belgorod',
                'seo_description' => 'Read the Privacy Policy for Photo Guide Belgorod.',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'excerpt' => 'Rules and conditions for using the website.',
                'content' => "These Terms and Conditions govern your use of Photo Guide Belgorod.\n\nBy using this website, you agree to follow these terms.\n\nIf you disagree with any part, please stop using the website.",
                'seo_title' => 'Terms and Conditions - Photo Guide Belgorod',
                'seo_description' => 'Read the Terms and Conditions for Photo Guide Belgorod.',
                'sort_order' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            LegalPage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                $page,
            );
        }
    }
}
