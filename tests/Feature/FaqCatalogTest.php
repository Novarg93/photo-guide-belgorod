<?php

use App\Models\Faq;
use Inertia\Testing\AssertableInertia as Assert;

it('shows only active faq entries on welcome page', function () {
    Faq::factory()->create([
        'question' => 'Active FAQ',
        'is_active' => true,
        'sort_order' => 10,
    ]);

    Faq::factory()->create([
        'question' => 'Hidden FAQ',
        'is_active' => false,
        'sort_order' => 5,
    ]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('faqs', 1)
            ->where('faqs.0.question', 'Active FAQ'));
});

it('seeds default faq entries', function () {
    $this->seed(\Database\Seeders\FaqSeeder::class);

    expect(Faq::query()->count())->toBeGreaterThanOrEqual(6);
});
