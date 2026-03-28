<?php

use App\Http\Controllers\AboutUsController;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

it('renders about us page', function () {
    $this->get(route('about'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AboutUs')
            ->where('metaTitle', 'About Us')
            ->where('metaDescription', fn (string $description): bool => $description !== ''));
});

it('binds about route to about us controller', function () {
    expect(Route::getRoutes()->getByName('about')?->getActionName())
        ->toBe(AboutUsController::class);
});
