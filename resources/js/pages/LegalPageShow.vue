<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

interface LegalPage {
    title: string;
    slug: string;
    excerpt: string | null;
    content: string;
}

const props = defineProps<{
    page: LegalPage;
    metaTitle: string;
    metaDescription: string;
}>();

useHead(() => ({
    title: props.metaTitle,
    meta: [
        { key: 'description', name: 'description', content: props.metaDescription },
    ],
}));
</script>

<template>
    <AppHeaderLayout>
        <section class="mx-auto w-full max-w-4xl py-8">
            <Link href="/catalog" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to catalog</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                {{ page.title }}
            </h1>

            <p v-if="page.excerpt" class="mt-3 text-zinc-600">
                {{ page.excerpt }}
            </p>

            <article class="prose prose-zinc mt-8 max-w-none whitespace-pre-line">
                {{ page.content }}
            </article>
        </section>
    </AppHeaderLayout>
</template>
