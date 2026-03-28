<?php

use App\Http\Controllers\ContactController;
use App\Models\ContactInquiry;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

it('renders contact us page', function () {
    $this->get(route('contact'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ContactUs')
            ->where('metaTitle', 'Contact Us'));
});

it('binds contact routes to contact controller', function () {
    expect(Route::getRoutes()->getByName('contact')?->getActionName())
        ->toBe(ContactController::class.'@index');

    expect(Route::getRoutes()->getByName('contact.store')?->getActionName())
        ->toBe(ContactController::class.'@store');
});

it('stores contact inquiry from form', function () {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1 202 555 0101',
        'message' => 'I want to plan a photo session in Belgorod.',
    ];

    $this->post(route('contact.store'), $payload)
        ->assertRedirect(route('contact'));

    $this->assertDatabaseHas('contact_inquiries', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1 202 555 0101',
        'is_processed' => false,
    ]);
});

it('validates required contact fields', function () {
    $this->from(route('contact'))
        ->post(route('contact.store'), [
            'name' => '',
            'email' => '',
            'message' => '',
        ])
        ->assertRedirect(route('contact'))
        ->assertSessionHasErrors(['name', 'email', 'message']);

    expect(ContactInquiry::query()->count())->toBe(0);
});
