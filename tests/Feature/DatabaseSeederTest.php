<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

it('creates verified admin user in database seeder', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::query()->where('email', 'admin@admin.admin')->first();

    expect($admin)->not->toBeNull()
        ->and($admin?->email_verified_at)->not->toBeNull()
        ->and(Hash::check('admin@admin.admin', (string) $admin?->password))->toBeTrue();
});
