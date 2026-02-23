<?php

use App\Models\Photographer;
use Inertia\Testing\AssertableInertia as Assert;

it('shows active photographers on photographers page', function () {
    Photographer::factory()->create([
        'name' => 'Active Photographer',
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
            ->where('photographers.0.name', 'Active Photographer'));
});

it('seeds ten default photographers', function () {
    $this->seed(\Database\Seeders\PhotographerSeeder::class);

    expect(Photographer::query()->count())->toBeGreaterThanOrEqual(10);
});
