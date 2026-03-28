<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, BookOpenText, CalendarDays, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

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
}>()

const articleInsights = computed(() => {
    return [
        {
            title: 'Article format',
            description: 'A practical guide page designed for quick reading before moving back into the catalog flow.',
        },
        {
            title: 'Use case',
            description: 'Helpful when you need extra context before choosing a location, category, or photographer.',
        },
        {
            title: 'Reading mode',
            description: 'Clean typography, focused spacing, and a quieter layout for longer text blocks.',
        },
    ]
})

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
                            href="/blogs"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>Back to guides</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-end">
                            <div>
                                <p
                                    v-if="blog.published_at"
                                    class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]"
                                >
                                    <CalendarDays class="h-3.5 w-3.5" />
                                    <span>{{ blog.published_at }}</span>
                                </p>

                                <h1 class="mt-4 max-w-4xl text-balance font-onest text-[38px] font-medium leading-[0.94] tracking-[-0.03em] text-[#20243B] md:text-[58px]">
                                    {{ blog.title }}
                                </h1>

                                <p
                                    v-if="blog.excerpt"
                                    class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base"
                                >
                                    {{ blog.excerpt }}
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[18px] bg-primary text-white">
                                        <BookOpenText class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <p class="font-onest text-sm font-medium text-[#A0A3B8]">Guide page</p>
                                        <p class="mt-2 text-sm leading-6 text-[#303651]">
                                            Read the article in full, then return to the catalog with clearer context for your next step.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 rounded-[18px] bg-[#F7F8FF] px-4 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Slug</p>
                                    <p class="mt-2 break-all font-onest text-lg font-medium text-[#20243B]">
                                        {{ blog.slug }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[0.72fr_0.28fr]">
                    <article class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6">
                        <div class="blog-show-image-shell rounded-[26px] bg-[#F7F8FF] p-3">
                            <div class="overflow-hidden rounded-[22px] border border-black/6 bg-[#E8E8E8]">
                                <img
                                    :src="blog.image_url"
                                    :alt="blog.title"
                                    class="blog-show-image h-[260px] w-full object-cover md:h-[420px]"
                                >
                            </div>
                        </div>

                        <div class="mt-6 rounded-[26px] border border-[#E2E5F6] bg-[#FCFCFF] p-5 md:p-6">
                            <div class="blog-show-prose prose prose-zinc max-w-none whitespace-pre-line text-zinc-700">
                                {{ blog.content }}
                            </div>
                        </div>
                    </article>

                    <aside class="space-y-4">
                        <article
                            v-for="insight in articleInsights"
                            :key="insight.title"
                            class="blog-show-note rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                        >
                            <div class="flex h-11 w-11 items-center justify-center rounded-[16px] bg-primary text-white">
                                <Sparkles class="h-5 w-5" />
                            </div>

                            <h2 class="mt-5 font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                {{ insight.title }}
                            </h2>

                            <p class="mt-4 text-sm leading-6 text-[#5C6079]">
                                {{ insight.description }}
                            </p>
                        </article>

                        <Link
                            href="/blogs"
                            class="blog-show-back group block rounded-[28px] bg-[#20243B] p-5 text-white shadow-[0px_18px_40px_rgba(20,23,45,0.14)]"
                        >
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Continue reading</p>
                            <p class="mt-4 font-onest text-[24px] font-medium leading-none">Back to all guides</p>
                            <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-white/14 bg-white/8 px-4 py-2 text-sm font-medium text-white">
                                <span>Open list</span>
                                <ArrowRight class="blog-show-back-arrow h-4 w-4" />
                            </div>
                        </Link>
                    </aside>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>
