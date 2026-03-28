<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Sparkles } from 'lucide-vue-next'
import { useRevealOnScroll } from '@/composables/useRevealOnScroll'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

const props = defineProps<{
    metaTitle: string;
    metaDescription: string;
}>()

const { sectionRef, isVisible } = useRevealOnScroll({
    threshold: 0.12,
    rootMargin: '0px 0px -10% 0px',
    once: true,
})

const pillars = [
    {
        title: 'Why this project exists',
        description:
            'We created this project to organize local photo session knowledge in one place: categories, references, locations, photographers, and planning tips.',
    },
    {
        title: 'Problem we solve',
        description:
            'Most people spend too much time searching across chats and social media. We reduce this chaos with structured filters, ready examples, and a clear brief flow.',
    },
    {
        title: 'Our plans',
        description:
            'Next steps are deeper content coverage, better filtering quality, richer local guides, and more practical tools for faster communication between clients and photographers.',
    },
] as const

useHead(() => ({
    title: props.metaTitle,
    meta: [{ key: 'description', name: 'description', content: props.metaDescription }],
}))
</script>

<template>
    <AppHeaderLayout>
        <section ref="sectionRef" class="bg-[#f5f5f5] pt-32 pb-64 md:pb-80">
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
                            href="/"
                            :class="[
                                'about-eyebrow-enter inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm',
                                isVisible && 'reveal-active',
                            ]"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>Back to home</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
                            <div>
                                <h1
                                    :class="[
                                        'about-title-enter text-[#20243B]',
                                        isVisible && 'reveal-active',
                                    ]"
                                >
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">
                                        About
                                    </span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">
                                        Us
                                    </span>
                                </h1>

                                <p
                                    :class="[
                                        'about-subtitle-enter mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base',
                                        isVisible && 'reveal-active',
                                    ]"
                                >
                                    Photo Guide Belgorod is a local content project that helps people plan photo sessions faster and with more confidence.
                                </p>
                            </div>

                            <div
                                :class="[
                                    'about-panel-enter rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]',
                                    isVisible && 'reveal-active',
                                ]"
                            >
                                <p class="font-onest text-sm font-medium text-[#A0A3B8]">Project focus</p>
                                <div class="mt-3 space-y-3">
                                    <div class="rounded-[18px] bg-[#F7F8FF] px-4 py-3 text-sm leading-6 text-[#303651]">
                                        Local categories, references, locations, photographers, and practical guidance.
                                    </div>
                                    <div class="inline-flex items-center gap-2 rounded-full border border-[#D9DCF3] bg-white px-3 py-1.5 text-xs font-medium text-[#4252FF]">
                                        <ArrowRight class="h-3.5 w-3.5" />
                                        Structured planning instead of scattered chats
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <article
                        v-for="(pillar, index) in pillars"
                        :key="pillar.title"
                        :class="[
                            'about-card-enter about-card relative overflow-hidden rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]',
                            isVisible && 'reveal-active',
                        ]"
                        :style="{ '--reveal-delay': `${0.2 + index * 0.08}s` }"
                    >
                        <div class="about-card-accent"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between gap-4">
                                <div class="about-card-index flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-sm font-semibold text-white">
                                    {{ String(index + 1).padStart(2, '0') }}
                                </div>

                                <div class="about-card-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]">
                                    <ArrowRight class="h-4 w-4" />
                                </div>
                            </div>

                            <h2 class="about-card-title mt-5 font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                {{ pillar.title }}
                            </h2>

                            <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>

                            <p class="mt-5 text-sm leading-6 text-[#5C6079]">
                                {{ pillar.description }}
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>
