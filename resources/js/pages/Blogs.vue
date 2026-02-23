<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

interface BlogCard {
    id: number;
    title: string;
    excerpt: string | null;
    image_url: string;
    published_at: string | null;
    url: string;
}

const props = defineProps<{
    blogs: BlogCard[];
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
        <section class="mx-auto w-full max-w-7xl py-8">
            <Link href="/catalog" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to catalog</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">Blog</h1>

            <p class="mt-3 max-w-2xl text-zinc-600">Tips, guides, and practical recommendations for photo sessions.</p>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="blog in blogs"
                    :key="blog.id"
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white"
                >
                    <div class="aspect-16/10 overflow-hidden bg-zinc-100">
                        <img :src="blog.image_url" :alt="blog.title" class="h-full w-full object-cover" loading="lazy" />
                    </div>

                    <div class="space-y-2 p-4">
                        <p v-if="blog.published_at" class="text-xs text-zinc-500">{{ blog.published_at }}</p>
                        <h2 class="text-lg font-semibold text-zinc-900">{{ blog.title }}</h2>
                        <p v-if="blog.excerpt" class="line-clamp-3 text-sm text-zinc-600">{{ blog.excerpt }}</p>

                        <Link
                            :href="blog.url"
                            class="inline-flex text-sm font-medium text-zinc-900 underline underline-offset-4 transition hover:text-zinc-600"
                        >
                            Read article
                        </Link>
                    </div>
                </article>
            </div>

            <div
                v-if="blogs.length === 0"
                class="mt-8 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600"
            >
                Blog is empty.
            </div>
        </section>
    </AppHeaderLayout>
</template>
