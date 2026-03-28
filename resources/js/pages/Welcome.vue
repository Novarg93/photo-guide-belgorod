<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import { Button } from '@/components/ui/button';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { catalog } from '@/routes';
import Hero from '@/components/Hero.vue';
import Categories from '@/components/Categories.vue';
import Locations from '@/components/Locations.vue';
import Photographers from '@/components/Photographers.vue';
import Faq from '@/components/Faq.vue';


const props = defineProps<{
    categories: Array<{
        id: number;
        name: string;
        title: string | null;
        description: string | null;
        url: string;
        image: string | null;
    }>;
    locations: Array<{
        id: number;
        name: string;
        description: string | null;
        image_url: string;
        url: string;
    }>;
    photographers: Array<{
        id: number;
        name: string;
        description: string | null;
        image_url: string;
        url: string | null;
    }>;
    blogs: Array<{
        id: number;
        title: string;
        excerpt: string | null;
        image_url: string;
        published_at: string | null;
        url: string;
    }>;
    faqs: Array<{
        id: number;
        question: string;
        answer: string;
    }>;
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
        <Hero />
        <Categories :categories="categories" />
        <Locations :locations="locations" />
        <Photographers />
        <Faq :faqs="faqs" />
        <!-- <section class="mx-auto w-full max-w-5xl py-12 md:py-20">
            <p class="mb-4 inline-flex rounded-full border border-zinc-200 bg-white/70 px-4 py-1 text-xs font-medium uppercase tracking-[0.2em] text-zinc-500">
                Photo Guide Belgorod
            </p>

            <h1 class="max-w-3xl text-4xl leading-tight font-semibold tracking-tight text-zinc-950 md:text-6xl">
                Choose your photo session style in Belgorod in minutes
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-relaxed text-zinc-600 md:text-lg">
                Browse categories, pick the right session type, and move to the next planning step.
            </p>

            <div class="mt-10">
                <Button as-child size="lg">
                    <Link :href="catalog()">Choose a photo session</Link>
                </Button>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 md:py-10">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">Categories</h2>
                <Link href="/catalog" class="text-sm text-zinc-600 transition hover:text-zinc-900">View all</Link>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="category.url"
                    class="rounded-2xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-400"
                >
                    <h3 class="text-lg font-semibold text-zinc-900">{{ category.title || category.name }}</h3>
                    <p class="mt-2 text-sm text-zinc-600">{{ category.description || 'No description yet.' }}</p>
                </Link>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 md:py-10">
            <div class="mb-5">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">How it works</h2>
                <p class="mt-2 max-w-2xl text-zinc-600">Plan your photo session in three simple steps.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs font-semibold tracking-wide text-zinc-500 uppercase">Step 1</p>
                    <h3 class="mt-2 text-lg font-semibold text-zinc-900">Choose your style</h3>
                    <p class="mt-2 text-sm text-zinc-600">Select a category that matches the mood and format you want.</p>
                </article>

                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs font-semibold tracking-wide text-zinc-500 uppercase">Step 2</p>
                    <h3 class="mt-2 text-lg font-semibold text-zinc-900">Review matching references</h3>
                    <p class="mt-2 text-sm text-zinc-600">Browse examples and locations that fit your selected style and filters.</p>
                </article>

                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs font-semibold tracking-wide text-zinc-500 uppercase">Step 3</p>
                    <h3 class="mt-2 text-lg font-semibold text-zinc-900">Send a brief to a photographer</h3>
                    <p class="mt-2 text-sm text-zinc-600">Share your final brief so the photographer can quickly understand your request.</p>
                </article>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 md:py-10">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">Popular Locations</h2>
                <Link href="/locations" class="text-sm text-zinc-600 transition hover:text-zinc-900">View all</Link>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="location in locations"
                    :key="location.id"
                    :href="location.url"
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:border-zinc-400"
                >
                    <div class="aspect-16/10 bg-zinc-100">
                        <img :src="location.image_url" :alt="location.name" class="h-full w-full object-cover" loading="lazy" />
                    </div>
                    <div class="p-4">
                        <h3 class="text-base font-semibold text-zinc-900">{{ location.name }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-zinc-600">{{ location.description || 'No description yet.' }}</p>
                    </div>
                </Link>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 md:py-10">
            <div class="mb-5">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">Why we are more convenient?</h2>
                <p class="mt-2 max-w-3xl text-zinc-600">Everything you need to plan a photo session faster and with less stress.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h3 class="text-base font-semibold text-zinc-900">Filter-based selection</h3>
                    <p class="mt-2 text-sm text-zinc-600">Use filters to quickly narrow down styles, mood, and matching examples.</p>
                </article>
                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h3 class="text-base font-semibold text-zinc-900">Ready-made ideas</h3>
                    <p class="mt-2 text-sm text-zinc-600">Get curated references and practical concepts you can use right away.</p>
                </article>
                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h3 class="text-base font-semibold text-zinc-900">Local places</h3>
                    <p class="mt-2 text-sm text-zinc-600">Discover locations in Belgorod that are suitable for different session types.</p>
                </article>
                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h3 class="text-base font-semibold text-zinc-900">Photographers list</h3>
                    <p class="mt-2 text-sm text-zinc-600">Browse available photographers and open their profiles directly.</p>
                </article>
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 sm:col-span-2 lg:col-span-2">
                    <h3 class="text-base font-semibold text-zinc-900">Time saving</h3>
                    <p class="mt-2 text-sm text-zinc-600">Collect decisions in one brief and move from idea to booking much faster.</p>
                </article>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 md:py-10">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">Photographers</h2>
                <Link href="/photographers" class="text-sm text-zinc-600 transition hover:text-zinc-900">View all</Link>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="photographer in photographers"
                    :key="photographer.id"
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white"
                >
                    <div class="aspect-16/10 bg-zinc-100">
                        <img :src="photographer.image_url" :alt="photographer.name" class="h-full w-full object-cover" loading="lazy" />
                    </div>
                    <div class="p-4">
                        <h3 class="text-base font-semibold text-zinc-900">{{ photographer.name }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-zinc-600">{{ photographer.description || 'No description yet.' }}</p>
                        <a
                            v-if="photographer.url"
                            :href="photographer.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-3 inline-flex text-sm font-medium text-zinc-900 underline underline-offset-4"
                        >
                            Open profile
                        </a>
                    </div>
                </article>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 md:py-10">
            <div class="rounded-3xl border border-zinc-200 bg-white p-6 md:p-8">
                <p class="text-sm font-medium text-zinc-500">Not sure where to start?</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-900 md:text-3xl">Take the selection flow</h2>
                <p class="mt-3 max-w-2xl text-zinc-600">Start with the catalog and find the right style, references, and locations for your session.</p>
                <div class="mt-6">
                    <Button as-child>
                        <Link :href="catalog()">Start</Link>
                    </Button>
                </div>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 pb-16 md:py-10 md:pb-20">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">Blog</h2>
                <Link href="/blogs" class="text-sm text-zinc-600 transition hover:text-zinc-900">View all</Link>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="blog in blogs"
                    :key="blog.id"
                    :href="blog.url"
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:border-zinc-400"
                >
                    <div class="aspect-16/10 bg-zinc-100">
                        <img :src="blog.image_url" :alt="blog.title" class="h-full w-full object-cover" loading="lazy" />
                    </div>
                    <div class="p-4">
                        <p v-if="blog.published_at" class="text-xs text-zinc-500">{{ blog.published_at }}</p>
                        <h3 class="mt-1 line-clamp-2 text-base font-semibold text-zinc-900">{{ blog.title }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-zinc-600">{{ blog.excerpt || 'No excerpt yet.' }}</p>
                    </div>
                </Link>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl py-6 pb-16 md:py-10 md:pb-20">
            <div class="mb-5">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">FAQ</h2>
                <p class="mt-2 max-w-3xl text-zinc-600">Answers to common questions about planning your photo session.</p>
            </div>

            <div class="space-y-3">
                <article v-for="faq in faqs" :key="faq.id" class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h3 class="text-base font-semibold text-zinc-900">{{ faq.question }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600">{{ faq.answer }}</p>
                </article>
            </div>
        </section> -->
    </AppHeaderLayout>
</template>
