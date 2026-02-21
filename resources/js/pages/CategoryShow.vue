<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { catalog } from '@/routes';

interface Category {
    name: string;
    description: string | null;
}

interface ExampleItem {
    id: number;
    title: string;
    summary: string | null;
    mood: string | null;
    location_hint: string | null;
    season_hint: string | null;
    clothing_hint: string | null;
}

defineProps<{
    category: Category;
    examples: ExampleItem[];
    metaTitle: string;
    metaDescription: string;
}>();
</script>

<template>
    <AppHeaderLayout>
        <Head :title="metaTitle">
            <meta head-key="description" name="description" :content="metaDescription" />
        </Head>

        <section class="mx-auto w-full max-w-6xl py-8">
            <Link :href="catalog()" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to catalog</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                {{ category.name }}
            </h1>

            <p class="mt-4 max-w-3xl text-base leading-relaxed text-zinc-700">
                {{ category.description || 'Category description will be added later.' }}
            </p>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Card v-for="example in examples" :key="example.id" class="h-full border-zinc-200">
                    <CardHeader>
                        <CardTitle class="text-xl">{{ example.title }}</CardTitle>
                        <CardDescription>
                            {{ example.summary || 'Summary will be added later.' }}
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="flex flex-wrap gap-2">
                        <Badge v-if="example.mood" variant="secondary">Mood: {{ example.mood }}</Badge>
                        <Badge v-if="example.location_hint" variant="outline">Location: {{ example.location_hint }}</Badge>
                        <Badge v-if="example.season_hint" variant="outline">Season: {{ example.season_hint }}</Badge>
                        <Badge v-if="example.clothing_hint" variant="outline">Clothing: {{ example.clothing_hint }}</Badge>
                    </CardContent>
                </Card>
            </div>

            <div
                v-if="examples.length === 0"
                class="mt-8 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600"
            >
                No examples published for this category yet.
            </div>
        </section>
    </AppHeaderLayout>
</template>
