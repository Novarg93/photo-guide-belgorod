<?php

use App\Models\PageSeo;
use Inertia\Testing\AssertableInertia as Assert;

it('uses seo settings from page seo records on public pages', function () {
    PageSeo::query()->updateOrCreate(
        ['page_key' => PageSeo::PAGE_WELCOME],
        [
            'seo_title' => 'Welcome SEO Title',
            'seo_description' => 'Welcome SEO Description',
        ],
    );
    PageSeo::query()->updateOrCreate(
        ['page_key' => PageSeo::PAGE_CATALOG],
        [
            'seo_title' => 'Catalog SEO Title',
            'seo_description' => 'Catalog SEO Description',
        ],
    );
    PageSeo::query()->updateOrCreate(
        ['page_key' => PageSeo::PAGE_LOCATIONS],
        [
            'seo_title' => 'Locations SEO Title',
            'seo_description' => 'Locations SEO Description',
        ],
    );

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('metaTitle', 'Welcome SEO Title')
            ->where('metaDescription', 'Welcome SEO Description'));

    $this->get(route('catalog'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Catalog')
            ->where('metaTitle', 'Catalog SEO Title')
            ->where('metaDescription', 'Catalog SEO Description'));

    $this->get(route('locations'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Locations')
            ->where('metaTitle', 'Locations SEO Title')
            ->where('metaDescription', 'Locations SEO Description'));
});
