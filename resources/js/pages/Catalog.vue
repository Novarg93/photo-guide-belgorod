<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { home } from '@/routes';

interface CategoryCard {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    url: string;
}

defineProps<{
    categories: CategoryCard[];
    metaTitle: string;
    metaDescription: string;
}>();
</script>

<template>
    <Head :title="metaTitle">
        <meta head-key="description" name="description" :content="metaDescription" />
    </Head>

    <main class="min-h-screen bg-zinc-50 px-6 py-10">
        <div class="mx-auto w-full max-w-6xl">
            <Link :href="home()" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to home</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                Photo Session Catalog
            </h1>

            <p class="mt-3 max-w-2xl text-zinc-600">
                Choose a category to continue.
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="category.url"
                    class="group rounded-2xl border border-zinc-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-zinc-900"
                >
                    <h2 class="text-lg font-semibold text-zinc-900 transition group-hover:text-zinc-700">
                        {{ category.name }}
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600">
                        {{ category.description || 'Category description will be added later.' }}
                    </p>
                </Link>
            </div>
        </div>
    </main>
</template>
