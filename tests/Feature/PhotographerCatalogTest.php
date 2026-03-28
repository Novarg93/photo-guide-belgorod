<?php

use App\Http\Controllers\PhotographersController;
use App\Models\Photographer;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

it('shows active photographers on photographers page', function () {
    Photographer::factory()->create([
        'name' => 'Active Photographer',
        'slug' => 'active-photographer',
        'is_active' => true,
    ]);

    Photographer::factory()->create([
        'name' => 'Inactive Photographer',
        'is_active' => false,
    ]);

    $this->get(route('photographers'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Photographers')
            ->has('photographers', 1)
            ->where('photographers.0.name', 'Active Photographer')
            ->where('photographers.0.slug', 'active-photographer')
            ->where('photographers.0.url', route('photographers.show', ['slug' => 'active-photographer'])));
});

it('binds photographers route to photographers controller', function () {
    expect(Route::getRoutes()->getByName('photographers')?->getActionName())
        ->toBe(PhotographersController::class.'@index');

    expect(Route::getRoutes()->getByName('photographers.show')?->getActionName())
        ->toBe(PhotographersController::class.'@show');
});

it('shows photographer detail page by slug', function () {
    $photographer = Photographer::factory()->create([
        'name' => 'Ольга Шевцова',
        'slug' => 'olga-shevtsova',
        'description' => 'Принимаю заказы на фотосъёмку различных жанров.',
        'is_active' => true,
    ]);

    $this->get(route('photographers.show', ['slug' => $photographer->slug]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PhotographerShow')
            ->where('photographer.name', 'Ольга Шевцова')
            ->where('photographer.slug', 'olga-shevtsova'));
});

it('uses semantic photographer detail url', function () {
    $photographer = Photographer::factory()->create([
        'slug' => 'olga-shevtsova',
        'is_active' => true,
    ]);

    expect(route('photographers.show', ['slug' => $photographer->slug], false))
        ->toBe('/photographer/olga-shevtsova');
});

it('returns not found for inactive photographer detail page', function () {
    $photographer = Photographer::factory()->create([
        'slug' => 'hidden-photographer',
        'is_active' => false,
    ]);

    $this->get(route('photographers.show', ['slug' => $photographer->slug]))
        ->assertNotFound();
});

it('seeds ten default photographers', function () {
    $this->seed(\Database\Seeders\PhotographerSeeder::class);

    expect(Photographer::query()->count())->toBeGreaterThanOrEqual(10);
    expect(Photographer::query()->where('slug', 'olga-shevtsova')->exists())->toBeTrue();
});
