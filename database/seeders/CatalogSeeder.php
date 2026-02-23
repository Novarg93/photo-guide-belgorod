<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PageSeoSeeder::class,
            LegalPageSeeder::class,
            CategorySeeder::class,
            ExampleSeeder::class,
            PhotoSeeder::class,
            LocationSeeder::class,
            PhotographerSeeder::class,
            BlogSeeder::class,
            FaqSeeder::class,
        ]);

        Category::query()
            ->select(['id', 'name'])
            ->get()
            ->each(function (Category $category): void {
                foreach (range(1, 4) as $slot) {
                    Photo::query()->updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'example_id' => null,
                            'title' => "{$category->name} Standalone {$slot}",
                        ],
                        [
                            'filter_option_keys' => [],
                            'path' => 'photos/placeholder.svg',
                            'source_type' => 'own',
                            'source_url' => null,
                            'author_name' => 'Catalog Seeder',
                            'license' => 'own',
                            'permission_note' => null,
                            'is_active' => true,
                        ],
                    );
                }
            });
    }
}
