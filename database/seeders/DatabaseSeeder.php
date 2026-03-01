<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            CatalogSeeder::class,
        ]);

        User::query()->firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        User::query()->firstOrCreate([
            'email' => 'admin@admin.admin',
        ], [
            'name' => 'Admin',
            'password' => 'admin@admin.admin',
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
