<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class PageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            [
                'page_key' => PageSeo::PAGE_WELCOME,
                'seo_title' => 'Photo sessions in Belgorod',
                'seo_description' => 'Choose a photo session category in Belgorod and continue to the next planning step.',
            ],
            [
                'page_key' => PageSeo::PAGE_CATALOG,
                'seo_title' => 'Photo Session Catalog',
                'seo_description' => 'Choose a category and continue to the next planning step.',
            ],
            [
                'page_key' => PageSeo::PAGE_LOCATIONS,
                'seo_title' => 'Locations Catalog',
                'seo_description' => 'All available photo locations in Belgorod.',
            ],
        ];

        foreach ($defaults as $default) {
            PageSeo::query()->updateOrCreate(
                ['page_key' => $default['page_key']],
                [
                    'seo_title' => $default['seo_title'],
                    'seo_description' => $default['seo_description'],
                ],
            );
        }
    }
}
