<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How do I choose the right photo session style?',
                'answer' => 'Start with a category that matches your mood and goal, then review references and examples.',
                'sort_order' => 10,
            ],
            [
                'question' => 'Can I pick locations based on filters?',
                'answer' => 'Yes, filters help you narrow down locations and references by style, mood, and preferences.',
                'sort_order' => 20,
            ],
            [
                'question' => 'Do you provide photographers contacts?',
                'answer' => 'Yes, each photographer card contains a profile link so you can contact them directly.',
                'sort_order' => 30,
            ],
            [
                'question' => 'How does the brief work?',
                'answer' => 'You select preferences in the constructor and get a ready brief that can be shared with a photographer.',
                'sort_order' => 40,
            ],
            [
                'question' => 'Can I use this website without registration?',
                'answer' => 'Yes, the catalog, locations, references, and brief creation are available without marketplace features.',
                'sort_order' => 50,
            ],
            [
                'question' => 'Is the content focused on Belgorod?',
                'answer' => 'Yes, this project is focused on local locations and practical photo session planning for Belgorod.',
                'sort_order' => 60,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::query()->updateOrCreate(
                ['question' => $faqData['question']],
                [
                    'answer' => $faqData['answer'],
                    'sort_order' => $faqData['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
