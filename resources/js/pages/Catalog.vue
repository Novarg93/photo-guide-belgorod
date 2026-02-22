<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { home } from '@/routes';

interface CategoryCard {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    filter_groups: {
        key: string;
        label: string;
        options: { key: string; label: string }[];
    }[];
    url: string;
}

defineProps<{
    categories: CategoryCard[];
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

                    <div v-if="category.filter_groups.length > 0" class="mt-4 space-y-2">
                        <div v-for="group in category.filter_groups" :key="group.key" class="space-y-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                {{ group.label }}
                            </p>
                            <p class="text-xs text-zinc-600">
                                {{ group.options.map((option) => option.label).join(', ') }}
                            </p>
                        </div>
                    </div>
                </Link>
            </div>
        </section>
    </AppHeaderLayout>
</template>
