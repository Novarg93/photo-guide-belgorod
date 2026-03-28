<?php

use App\Http\Controllers\LegalController;
use App\Models\LegalPage;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

it('shares active legal pages for footer navigation', function () {
    LegalPage::factory()->create([
        'title' => 'Privacy Policy',
        'slug' => 'privacy-policy',
        'sort_order' => 10,
        'is_active' => true,
    ]);

    LegalPage::factory()->create([
        'title' => 'Terms and Conditions',
        'slug' => 'terms-and-conditions',
        'sort_order' => 20,
        'is_active' => true,
    ]);

    LegalPage::factory()->create([
        'title' => 'Draft Legal',
        'slug' => 'draft-legal',
        'sort_order' => 5,
        'is_active' => false,
    ]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('legalPages', 2)
            ->where('legalPages.0.slug', 'privacy-policy')
            ->where('legalPages.1.slug', 'terms-and-conditions'));
});

it('renders a legal page by slug', function () {
    $page = LegalPage::factory()->create([
        'title' => 'Privacy Policy',
        'slug' => 'privacy-policy',
        'excerpt' => 'How we process data.',
        'content' => 'Legal content body.',
        'is_active' => true,
    ]);

    $this->get(route('legal.show', ['slug' => $page->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $response) => $response
            ->component('LegalPageShow')
            ->where('page.title', 'Privacy Policy')
            ->where('page.slug', 'privacy-policy')
            ->where('metaTitle', fn (string $title): bool => $title !== ''));
});

it('binds legal routes to legal controller', function () {
    expect(Route::getRoutes()->getByName('legal.show')?->getActionName())
        ->toBe(LegalController::class.'@show');

    expect(Route::getRoutes()->getByName('copyright')?->getActionName())
        ->toBe(LegalController::class.'@copyright');
});

it('returns not found for inactive legal page', function () {
    $page = LegalPage::factory()->create([
        'slug' => 'privacy-policy',
        'is_active' => false,
    ]);

    $this->get(route('legal.show', ['slug' => $page->slug]))
        ->assertNotFound();
});
