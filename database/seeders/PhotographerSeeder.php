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
            [
                'name' => 'Ольга Шевцова',
                'slug' => 'olga-shevtsova',
                'url' => 'https://example.com/olga-shevtsova',
                'description' => 'Принимаю заказы на фотосъёмку различных жанров и буду рада запечатлеть для вас важные события и моменты вашей жизни.',
            ],
            [
                'name' => 'Анна Миронова',
                'slug' => 'anna-mironova',
                'url' => 'https://example.com/anna-mironova',
                'description' => 'Работаю с семейными и женскими съёмками, помогая собрать спокойную и естественную серию кадров без лишней суеты.',
            ],
            [
                'name' => 'Дмитрий Орлов',
                'slug' => 'dmitry-orlov',
                'url' => 'https://example.com/dmitry-orlov',
                'description' => 'Снимаю пары, прогулки и персональные истории с акцентом на свет, ритм города и живые эмоции.',
            ],
            [
                'name' => 'Екатерина Белова',
                'slug' => 'ekaterina-belova',
                'url' => 'https://example.com/ekaterina-belova',
                'description' => 'Помогаю подобрать комфортный формат фотосессии и собрать визуально цельную историю под ваш запрос.',
            ],
            [
                'name' => 'Максим Кравцов',
                'slug' => 'maksim-kravtsov',
                'url' => 'https://example.com/maksim-kravtsov',
                'description' => 'Снимаю городские и репортажные съёмки, где важны движение, пространство и ощущение настоящего момента.',
            ],
            [
                'name' => 'Ирина Соколова',
                'slug' => 'irina-sokolova',
                'url' => 'https://example.com/irina-sokolova',
                'description' => 'Люблю мягкий свет, спокойные кадры и съёмки, в которых легко раскрывается характер человека или пары.',
            ],
            [
                'name' => 'Никита Волков',
                'slug' => 'nikita-volkov',
                'url' => 'https://example.com/nikita-volkov',
                'description' => 'Фокусируюсь на мужских, парных и lifestyle-съёмках с понятной структурой и собранным визуальным результатом.',
            ],
            [
                'name' => 'Ксения Зайцева',
                'slug' => 'kseniya-zaytseva',
                'url' => 'https://example.com/kseniya-zaytseva',
                'description' => 'Подскажу по образу, настроению и локации, чтобы итоговая съёмка выглядела цельно ещё до выхода на площадку.',
            ],
            [
                'name' => 'Павел Демин',
                'slug' => 'pavel-demin',
                'url' => 'https://example.com/pavel-demin',
                'description' => 'Снимаю короткие, насыщенные прогулочные серии и люблю находить выразительные точки даже в знакомых местах города.',
            ],
            [
                'name' => 'Софья Егорова',
                'slug' => 'sofya-egorova',
                'url' => 'https://example.com/sofya-egorova',
                'description' => 'Работаю с женскими и семейными съёмками, где важны деликатное сопровождение, мягкая подача и аккуратный результат.',
            ],
        ];

        foreach ($photographers as $photographer) {
            Photographer::query()->updateOrCreate(
                ['slug' => $photographer['slug']],
                [
                    'name' => $photographer['name'],
                    'slug' => $photographer['slug'],
                    'url' => $photographer['url'],
                    'image_path' => 'photos/placeholder.svg',
                    'description' => $photographer['description'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }
}
