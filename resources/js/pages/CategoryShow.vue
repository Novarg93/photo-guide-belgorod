<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { catalog } from '@/routes';

interface Category {
    name: string;
    description: string | null;
}

defineProps<{
    category: Category;
    metaTitle: string;
    metaDescription: string;
}>();
</script>

<template>
    <AppHeaderLayout>
        <Head :title="metaTitle">
            <meta head-key="description" name="description" :content="metaDescription" />
        </Head>

        <section class="mx-auto w-full max-w-4xl py-8">
            <Link :href="catalog()" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to catalog</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                {{ category.name }}
            </h1>

            <p class="mt-4 text-base leading-relaxed text-zinc-700">
                {{ category.description || 'Category description will be added later.' }}
            </p>

            <div class="mt-8 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600">
                Filters and examples will be added in the next phase.
            </div>
        </section>
    </AppHeaderLayout>
</template>
