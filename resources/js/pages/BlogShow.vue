<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

interface BlogItem {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string;
    image_url: string;
    published_at: string | null;
    url: string;
}

const props = defineProps<{
    blog: BlogItem;
    metaTitle: string;
    metaDescription: string;
}>();

useHead(() => ({
    title: props.metaTitle,
    meta: [{ key: 'description', name: 'description', content: props.metaDescription }],
}));
</script>

<template>
    <AppHeaderLayout>
        <article class="mx-auto w-full max-w-4xl py-8 md:py-10">
            <Link href="/blogs" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to blog</Link>

            <p v-if="blog.published_at" class="mt-4 text-sm text-zinc-500">{{ blog.published_at }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">{{ blog.title }}</h1>
            <p v-if="blog.excerpt" class="mt-4 text-zinc-600">{{ blog.excerpt }}</p>

            <div class="mt-6 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100">
                <img :src="blog.image_url" :alt="blog.title" class="h-full w-full object-cover" />
            </div>

            <div class="prose prose-zinc mt-8 max-w-none whitespace-pre-line text-zinc-700">
                {{ blog.content }}
            </div>
        </article>
    </AppHeaderLayout>
</template>
