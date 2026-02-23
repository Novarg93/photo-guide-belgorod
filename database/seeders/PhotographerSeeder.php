<?php

namespace Database\Seeders;

use App\Models\Photographer;
use Illuminate\Database\Seeder;

class PhotographerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photographers = [
            ['name' => 'Alex Morozov', 'url' => 'https://example.com/alex-morozov'],
            ['name' => 'Elena Petrova', 'url' => 'https://example.com/elena-petrova'],
            ['name' => 'Mikhail Sokolov', 'url' => 'https://example.com/mikhail-sokolov'],
            ['name' => 'Anna Belova', 'url' => 'https://example.com/anna-belova'],
            ['name' => 'Dmitry Leonov', 'url' => 'https://example.com/dmitry-leonov'],
            ['name' => 'Irina Volkova', 'url' => 'https://example.com/irina-volkova'],
            ['name' => 'Nikita Romanov', 'url' => 'https://example.com/nikita-romanov'],
            ['name' => 'Ksenia Zueva', 'url' => 'https://example.com/ksenia-zueva'],
            ['name' => 'Pavel Klimov', 'url' => 'https://example.com/pavel-klimov'],
            ['name' => 'Sofia Egorova', 'url' => 'https://example.com/sofia-egorova'],
        ];

        foreach ($photographers as $photographer) {
            Photographer::query()->updateOrCreate(
                ['name' => $photographer['name']],
                [
                    'url' => $photographer['url'],
                    'image_path' => 'photos/placeholder.svg',
                    'description' => "Portfolio and contact information for {$photographer['name']}.",
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }
}
