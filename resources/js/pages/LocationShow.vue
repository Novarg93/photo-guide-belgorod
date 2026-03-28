<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Images, MapPinned, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

interface LocationPageData {
    name: string;
    slug: string;
    description: string | null;
    image_url: string;
    category: string | null;
    example_photos: string[];
    url: string;
}

const props = defineProps<{
    location: LocationPageData;
    metaTitle: string;
    metaDescription: string;
}>()

const jsonLd = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Place',
        name: props.location.name,
        description: props.metaDescription,
        image: props.location.image_url,
        url: props.location.url,
        address: {
            '@type': 'PostalAddress',
            addressLocality: 'Belgorod',
            addressCountry: 'RU',
        },
    }),
)

const locationDescription = computed<string>(() => {
    if (props.location.description) {
        return props.location.description
    }

    return `Location ${props.location.name} is suitable for photo sessions in Belgorod. Browse examples and choose a matching style.`
})

const locationInsights = computed(() => {
    return [
        {
            title: 'Local context',
            description: 'A dedicated Belgorod location page prepared for browsing, comparisons, and planning.',
        },
        {
            title: 'Reference view',
            description: 'Use the gallery below to estimate the mood, framing options, and general visual potential.',
        },
        {
            title: 'Next step',
            description: 'After reviewing the location, move back to the catalog or continue into a matching category.',
        },
    ]
})

useHead(() => ({
    title: props.metaTitle,
    meta: [
        { key: 'description', name: 'description', content: props.metaDescription },
        { key: 'og:title', property: 'og:title', content: props.metaTitle },
        { key: 'og:description', property: 'og:description', content: props.metaDescription },
        { key: 'og:type', property: 'og:type', content: 'website' },
        { key: 'og:image', property: 'og:image', content: props.location.image_url },
        { key: 'og:url', property: 'og:url', content: props.location.url },
        { key: 'twitter:card', name: 'twitter:card', content: 'summary_large_image' },
    ],
    link: [
        { key: 'canonical', rel: 'canonical', href: props.location.url },
    ],
    script: [
        { key: 'location-jsonld', type: 'application/ld+json', children: jsonLd.value },
    ],
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
                            href="/locations"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>Back to locations</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-end">
                            <div>
                                <p
                                    v-if="location.category"
                                    class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]"
                                >
                                    <MapPinned class="h-3.5 w-3.5" />
                                    <span>{{ location.category }}</span>
                                </p>

                                <h1 class="mt-4 max-w-4xl text-balance font-onest text-[38px] font-medium leading-[0.94] tracking-[-0.03em] text-[#20243B] md:text-[58px]">
                                    {{ location.name }}
                                </h1>

                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    {{ locationDescription }}
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-[20px] bg-[#F7F8FF] px-4 py-4">
                                        <p class="font-onest text-sm font-medium text-[#A0A3B8]">Example photos</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none text-[#20243B]">{{ location.example_photos.length }}</span>
                                            <span class="pb-1 text-sm text-[#5C6079]">images to review</span>
                                        </div>
                                    </div>

                                    <div class="rounded-[20px] bg-[#20243B] px-4 py-4 text-white">
                                        <p class="font-onest text-sm font-medium text-white/60">Location slug</p>
                                        <p class="mt-3 break-all font-onest text-xl font-medium leading-tight">
                                            {{ location.slug }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-[18px] border border-[#E2E5F6] bg-white px-4 py-3 text-sm leading-6 text-[#303651]">
                                    Review the location first, then use the gallery to understand how this place behaves in a real photo session context.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[0.72fr_0.28fr]">
                    <article class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6">
                        <div class="location-show-image-shell rounded-[26px] bg-[#F7F8FF] p-3">
                            <div class="overflow-hidden rounded-[22px] border border-black/6 bg-[#E8E8E8]">
                                <img
                                    :src="location.image_url"
                                    :alt="location.name"
                                    class="location-show-image h-[260px] w-full object-cover md:h-[420px]"
                                >
                            </div>
                        </div>

                        <div class="mt-6 rounded-[26px] border border-[#E2E5F6] bg-[#FCFCFF] p-5 md:p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[18px] bg-primary text-white">
                                    <MapPinned class="h-5 w-5" />
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Location overview</p>
                                    <p class="mt-3 text-sm leading-7 text-[#4F556F] md:text-base">
                                        {{ locationDescription }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Gallery</p>
                                    <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">
                                        Photo session examples
                                    </h2>
                                </div>

                                <div class="hidden h-12 w-12 items-center justify-center rounded-[18px] bg-[#F7F8FF] text-[#4252FF] md:flex">
                                    <Images class="h-5 w-5" />
                                </div>
                            </div>

                            <div
                                v-if="location.example_photos.length"
                                class="mt-6 grid gap-5 sm:grid-cols-2"
                            >
                                <article
                                    v-for="(photoUrl, index) in location.example_photos"
                                    :key="`${location.slug}-${index}`"
                                    class="location-show-gallery-card overflow-hidden rounded-[26px] bg-[#F7F8FF] p-3"
                                >
                                    <div class="overflow-hidden rounded-[20px] border border-black/6 bg-[#E8E8E8]">
                                        <img
                                            :src="photoUrl"
                                            :alt="`${location.name} photo session example ${index + 1}`"
                                            class="location-show-gallery-image h-[220px] w-full object-cover"
                                            loading="lazy"
                                        >
                                    </div>
                                </article>
                            </div>

                            <div
                                v-else
                                class="mt-6 rounded-[26px] border border-dashed border-[#D9DCF3] bg-[#F7F8FF] p-5 text-sm leading-6 text-[#5C6079]"
                            >
                                No examples are available for this location yet.
                            </div>
                        </div>
                    </article>

                    <aside class="space-y-4">
                        <article
                            v-for="insight in locationInsights"
                            :key="insight.title"
                            class="location-show-note rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
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
                            href="/locations"
                            class="location-show-back group block rounded-[28px] bg-[#20243B] p-5 text-white shadow-[0px_18px_40px_rgba(20,23,45,0.14)]"
                        >
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Continue browsing</p>
                            <p class="mt-4 font-onest text-[24px] font-medium leading-none">Back to all locations</p>
                            <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-white/14 bg-white/8 px-4 py-2 text-sm font-medium text-white">
                                <span>Open catalog</span>
                                <ArrowRight class="location-show-back-arrow h-4 w-4" />
                            </div>
                        </Link>
                    </aside>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>
