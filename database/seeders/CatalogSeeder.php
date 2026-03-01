<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Photo;
use App\Support\CategoryFilterSchema;
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
            ->select(['id', 'name', 'filter_groups'])
            ->get()
            ->each(function (Category $category): void {
                $allowedFilterKeys = CategoryFilterSchema::allowedOptionKeys($category->filter_groups);

                foreach (range(1, 4) as $slot) {
                    Photo::query()->updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'example_id' => null,
                            'title' => "{$category->name} Standalone {$slot}",
                        ],
                        [
                            'filter_option_keys' => $this->buildMixedFilterKeys($allowedFilterKeys, $slot),
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

    /**
     * @param  array<int, string>  $allowedFilterKeys
     * @return array<int, string>
     */
    private function buildMixedFilterKeys(array $allowedFilterKeys, int $offset): array
    {
        if ($allowedFilterKeys === []) {
            return [];
        }

        $desiredCount = min(count($allowedFilterKeys), min(4, 2 + ($offset % 3)));
        $normalizedOffset = $offset % count($allowedFilterKeys);
        $rotatedFilterKeys = array_values([
            ...array_slice($allowedFilterKeys, $normalizedOffset),
            ...array_slice($allowedFilterKeys, 0, $normalizedOffset),
        ]);

        return array_values(array_slice($rotatedFilterKeys, 0, $desiredCount));
    }
}
