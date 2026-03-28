<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, BookOpenText, CalendarDays, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

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
}>()

const publishedArticlesCount = computed<number>(() => {
    return props.blogs.filter((blog) => blog.published_at).length
})

const blogHighlights = [
    {
        title: 'Useful reading',
        description: 'Collect practical recommendations, references, and planning tips in one section.',
    },
    {
        title: 'Quick scanning',
        description: 'Preview each article by image, title, excerpt, and publication date before opening it.',
    },
    {
        title: 'Planning support',
        description: 'Use the guides alongside categories, locations, and photographers while preparing a shoot.',
    },
]

const blogExcerpt = (blog: BlogCard): string => {
    if (blog.excerpt) {
        return blog.excerpt
    }

    return 'A practical guide from the Belgorod photo session catalog.'
}

useHead(() => ({
    title: props.metaTitle,
    meta: [{ key: 'description', name: 'description', content: props.metaDescription }],
}))
</script>

<template>
    <AppHeaderLayout>
        <section class="bg-[#f5f5f5] pt-32 pb-64 md:pb-80">
            <div class="mx-auto max-w-7xl px-5">
                <div class="relative overflow-hidden rounded-[32px] bg-card px-5 py-8 md:px-8 md:py-10">
                    <div class="grid-overlay-hero">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>

                    <div class="relative z-10">
                        <Link
                            href="/catalog"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>Back to catalog</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
                            <div>
                                <h1 class="text-[#20243B]">
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">
                                        Photo
                                    </span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">
                                        Guides
                                    </span>
                                </h1>

                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    Tips, guides, and practical recommendations that help move from inspiration to a more structured photo session plan.
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-[20px] bg-[#F7F8FF] px-4 py-4">
                                        <p class="font-onest text-sm font-medium text-[#A0A3B8]">Available articles</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none text-[#20243B]">{{ blogs.length }}</span>
                                            <span class="pb-1 text-sm text-[#5C6079]">guides to read</span>
                                        </div>
                                    </div>

                                    <div class="rounded-[20px] bg-[#20243B] px-4 py-4 text-white">
                                        <p class="font-onest text-sm font-medium text-white/60">Published entries</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none">{{ publishedArticlesCount }}</span>
                                            <span class="pb-1 text-sm text-white/70">with visible dates</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-[18px] border border-[#E2E5F6] bg-white px-4 py-3 text-sm leading-6 text-[#303651]">
                                    Use this page as a reading layer inside the product, before diving into specific categories, locations, or photographers.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[0.72fr_0.28fr]">
                    <div
                        v-if="blogs.length > 0"
                        class="grid gap-5 sm:grid-cols-2"
                    >
                        <Link
                            v-for="blog in blogs"
                            :key="blog.id"
                            :href="blog.url"
                            class="blogs-page-card group block overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                        >
                            <div class="blogs-page-card-accent"></div>

                            <div class="relative z-10">
                                <div class="blogs-page-card-media rounded-[22px] border-b border-dashed border-black/8 bg-white p-2">
                                    <div class="overflow-hidden rounded-[18px] bg-[#E8E8E8]">
                                        <img
                                            :src="blog.image_url"
                                            :alt="blog.title"
                                            class="blogs-page-card-image h-[220px] w-full object-cover"
                                            loading="lazy"
                                        >
                                    </div>
                                </div>

                                <div class="mt-4 flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="blogs-page-card-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-white">
                                            <BookOpenText class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <p
                                                v-if="blog.published_at"
                                                class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]"
                                            >
                                                <CalendarDays class="h-3.5 w-3.5" />
                                                <span>{{ blog.published_at }}</span>
                                            </p>

                                            <h2 class="blogs-page-card-title mt-2 font-onest text-[24px] font-medium leading-tight text-[#20243B]">
                                                {{ blog.title }}
                                            </h2>
                                        </div>
                                    </div>

                                    <div class="blogs-page-card-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]">
                                        <ArrowRight class="h-4 w-4" />
                                    </div>
                                </div>

                                <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>

                                <p class="blogs-page-card-description mt-5 text-sm leading-6 text-[#5C6079]">
                                    {{ blogExcerpt(blog) }}
                                </p>

                                <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-[#D9DCF3] bg-white px-4 py-2 text-sm font-medium text-[#4252FF]">
                                    <span>Read article</span>
                                    <ArrowRight class="h-4 w-4" />
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div
                        v-else
                        class="rounded-[28px] bg-white p-6 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                    >
                        <p class="font-onest text-[24px] font-medium text-[#20243B]">Blog is empty.</p>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-[#5C6079]">
                            Add guide entries in the admin panel and they will appear here in the public catalog.
                        </p>
                    </div>

                    <aside class="space-y-4">
                        <article
                            v-for="highlight in blogHighlights"
                            :key="highlight.title"
                            class="blogs-page-note rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                        >
                            <div class="flex h-11 w-11 items-center justify-center rounded-[16px] bg-primary text-white">
                                <Sparkles class="h-5 w-5" />
                            </div>

                            <h2 class="mt-5 font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                {{ highlight.title }}
                            </h2>

                            <p class="mt-4 text-sm leading-6 text-[#5C6079]">
                                {{ highlight.description }}
                            </p>
                        </article>
                    </aside>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>
