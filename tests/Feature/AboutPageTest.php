<?php

use Inertia\Testing\AssertableInertia as Assert;

it('renders about us page', function () {
    $this->get(route('about'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AboutUs')
            ->where('metaTitle', 'About Us')
            ->where('metaDescription', fn (string $description): bool => $description !== ''));
});
