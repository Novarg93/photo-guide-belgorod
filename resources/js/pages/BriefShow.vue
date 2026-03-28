<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ArrowRight, CheckCheck, Copy, MapPinned, Share2, Sparkles } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'
import { catalog } from '@/routes'
import { show as showCategory } from '@/routes/categories'

interface Category {
    name: string;
    slug: string;
    description: string | null;
}

interface Filters {
    mood: string | null;
    season: string | null;
    location: string | null;
    clothing: string | null;
    active_filter_option_keys?: string[];
}

interface ExampleItem {
    id: string;
    title: string;
    summary: string | null;
    mood: string | null;
    location_hint: string | null;
    season_hint: string | null;
    clothing_hint: string | null;
    filter_option_labels: string[];
    image_url: string;
}

interface LocationItem {
    id: number;
    name: string;
    image_url: string;
    filter_option_labels: string[];
}

const props = defineProps<{
    category: Category;
    filters: Filters;
    locationFilterOptionLabels: string[];
    examples: ExampleItem[];
    locations: LocationItem[];
    selectedExampleIds: number[];
    peopleCount: string | null;
    notes: string | null;
    retouchPreference: string | null;
    colorStyle: string | null;
    shareUrl: string;
    token: string;
    metaTitle: string;
    metaDescription: string;
}>()

const copied = ref(false)

const selectedFilterBadges = computed(() => {
    const badges: Array<{ label: string; value: string }> = []

    if (props.filters.mood) {
        badges.push({ label: 'Mood', value: props.filters.mood })
    }

    if (props.filters.season) {
        badges.push({ label: 'Season', value: props.filters.season })
    }

    if (props.filters.location) {
        badges.push({ label: 'Location', value: props.filters.location })
    }

    if (props.filters.clothing) {
        badges.push({ label: 'Clothing', value: props.filters.clothing })
    }

    return badges
})

const backToCategoryUrl = computed(() => {
    const query: Record<string, string | string[]> = {}
    const activeFilterOptionKeys = props.filters.active_filter_option_keys ?? []

    if (activeFilterOptionKeys.length > 0) {
        query.filters = activeFilterOptionKeys
    }

    return showCategory.url({ slug: props.category.slug }, { query })
})

const briefText = computed(() => {
    const lines: string[] = [
        'Brief for photographer',
        `Category: ${props.category.name}`,
    ]

    if (selectedFilterBadges.value.length > 0) {
        lines.push('Filters:')

        selectedFilterBadges.value.forEach((badge) => {
            lines.push(`- ${badge.label}: ${badge.value}`)
        })
    } else {
        lines.push('Filters: not selected')
    }

    if (props.peopleCount) {
        lines.push(`People count: ${props.peopleCount}`)
    }

    if (props.notes) {
        lines.push(`Notes: ${props.notes}`)
    }

    if (props.retouchPreference) {
        lines.push(`Retouch: ${props.retouchPreference}`)
    }

    if (props.colorStyle) {
        lines.push(`Color: ${props.colorStyle}`)
    }

    lines.push(`Link: ${props.shareUrl}`)
    lines.push('Examples:')

    if (props.examples.length === 0) {
        lines.push('1. No matching examples')
    } else {
        props.examples.forEach((example, index) => {
            const hints: string[] = []

            if (example.mood) {
                hints.push(`mood: ${example.mood}`)
            }

            if (example.location_hint) {
                hints.push(`location: ${example.location_hint}`)
            }

            lines.push(`${index + 1}. ${example.title}${hints.length > 0 ? ` (${hints.join(', ')})` : ''}`)
        })
    }

    lines.push('Locations:')

    if (props.locationFilterOptionLabels.length > 0) {
        lines.push(`Applied location filters: ${props.locationFilterOptionLabels.join(', ')}`)
    }

    if (props.locations.length === 0) {
        lines.push('1. No matching locations')
    } else {
        props.locations.forEach((location, index) => {
            lines.push(`${index + 1}. ${location.name}`)
        })
    }

    return lines.join('\n')
})

const briefDetails = computed(() => {
    return [
        { label: 'People count', value: props.peopleCount },
        { label: 'Retouch preference', value: props.retouchPreference },
        { label: 'Color style', value: props.colorStyle },
        { label: 'Token', value: props.token },
    ].filter((item) => item.value)
})

const copyLink = async (): Promise<void> => {
    await navigator.clipboard.writeText(props.shareUrl)
    copied.value = true

    window.setTimeout(() => {
        copied.value = false
    }, 1500)
}

const copyBriefText = async (): Promise<void> => {
    await navigator.clipboard.writeText(briefText.value)
    copied.value = true

    window.setTimeout(() => {
        copied.value = false
    }, 1500)
}
</script>

<template>
    <AppHeaderLayout>
        <Head :title="metaTitle">
            <meta head-key="description" name="description" :content="metaDescription" />
        </Head>

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
                        <Link :href="catalog()" class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary"><Sparkles class="h-3.5 w-3.5" /></span>
                            <span>Back to catalog</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-end">
                            <div>
                                <h1 class="text-[#20243B]">
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">Brief</span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">Summary</span>
                                </h1>
                                <h2 class="mt-5 font-onest text-[28px] font-medium leading-none text-[#20243B] md:text-[34px]">{{ category.name }}</h2>
                                <p class="mt-4 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">{{ category.description || 'Category description will be added later.' }}</p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-[20px] bg-[#F7F8FF] px-4 py-4">
                                        <p class="font-onest text-sm font-medium text-[#A0A3B8]">Selected references</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none text-[#20243B]">{{ examples.length }}</span>
                                            <span class="pb-1 text-sm text-[#5C6079]">cards included</span>
                                        </div>
                                    </div>
                                    <div class="rounded-[20px] bg-[#20243B] px-4 py-4 text-white">
                                        <p class="font-onest text-sm font-medium text-white/60">Matching locations</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none">{{ locations.length }}</span>
                                            <span class="pb-1 text-sm text-white/70">places attached</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 rounded-[18px] border border-[#E2E5F6] bg-white px-4 py-3 text-sm leading-6 text-[#303651]">This page is ready to share with a photographer as a compact summary of your direction, chosen references, and filtered location suggestions.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[0.34fr_0.66fr]">
                    <aside class="space-y-5">
                        <div class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Sharing</p>
                            <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">Shareable brief</h2>
                            <p class="mt-4 text-sm leading-6 text-[#5C6079] break-all">{{ shareUrl }}</p>
                            <div class="mt-5 flex flex-col gap-3">
                                <Button class="brief-show-action" @click="copyLink"><Copy class="h-4 w-4" />Copy link</Button>
                                <Button variant="outline" class="brief-show-action" @click="copyBriefText"><Share2 class="h-4 w-4" />Copy brief text</Button>
                            </div>
                            <div v-if="copied" class="mt-4 inline-flex items-center gap-2 rounded-full border border-[#D9DCF3] bg-[#F7F8FF] px-4 py-2 text-sm font-medium text-[#4252FF]">
                                <CheckCheck class="h-4 w-4" />
                                <span>Copied</span>
                            </div>
                        </div>

                        <div class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Applied filters</p>
                            <div v-if="selectedFilterBadges.length > 0" class="mt-4 flex flex-wrap gap-2">
                                <Badge v-for="badge in selectedFilterBadges" :key="badge.label" variant="secondary" class="brief-show-chip border border-[#D9DCF3] bg-white px-3 py-1.5 text-[#4252FF]">{{ badge.label }}: {{ badge.value }}</Badge>
                            </div>
                            <p v-else class="mt-4 text-sm leading-6 text-[#5C6079]">No explicit top-level filters were saved for this brief.</p>
                        </div>

                        <div v-if="briefDetails.length > 0 || notes" class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Details</p>
                            <div v-if="briefDetails.length > 0" class="mt-4 space-y-3">
                                <div v-for="detail in briefDetails" :key="detail.label" class="rounded-[20px] bg-[#F7F8FF] px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">{{ detail.label }}</p>
                                    <p class="mt-2 text-sm leading-6 text-[#303651]">{{ detail.value }}</p>
                                </div>
                            </div>
                            <div v-if="notes" class="mt-4 rounded-[20px] border border-[#E2E5F6] bg-[#FCFCFF] px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Notes</p>
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-[#303651]">{{ notes }}</p>
                            </div>
                        </div>

                        <Link :href="backToCategoryUrl" class="brief-show-back group block rounded-[32px] bg-[#20243B] p-5 text-white shadow-[0px_18px_40px_rgba(20,23,45,0.14)]">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Continue editing</p>
                            <p class="mt-4 font-onest text-[28px] font-medium leading-none">Back to category</p>
                            <p class="mt-4 text-sm leading-6 text-white/72">Return to the category page with the saved filter state and keep refining the reference set.</p>
                            <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-white/14 bg-white/8 px-4 py-2 text-sm font-medium text-white"><span>Open category</span><ArrowRight class="brief-show-back-arrow h-4 w-4" /></div>
                        </Link>
                    </aside>

                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">References</p>
                                    <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">Selected examples</h2>
                                </div>
                            </div>

                            <div v-if="examples.length > 0" class="mt-6 grid gap-5 sm:grid-cols-2">
                                <article v-for="example in examples" :key="example.id" class="brief-show-example-card group overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                    <div class="brief-show-example-accent"></div>
                                    <div class="relative z-10">
                                        <div class="brief-show-example-media rounded-[22px] border-b border-dashed border-black/8 bg-white p-2"><div class="overflow-hidden rounded-[18px] bg-[#E8E8E8]"><img :src="example.image_url" :alt="example.title" class="brief-show-example-image h-[240px] w-full object-cover" loading="lazy" /></div></div>
                                        <div class="mt-4 flex items-start justify-between gap-4">
                                            <div>
                                                <h2 class="brief-show-example-title line-clamp-2 font-onest text-[24px] font-medium leading-tight text-[#20243B]">{{ example.title }}</h2>
                                                <p v-if="example.mood" class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">{{ example.mood }}</p>
                                            </div>
                                            <div class="brief-show-example-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]"><ArrowRight class="h-4 w-4" /></div>
                                        </div>
                                        <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>
                                        <div class="mt-5 space-y-3">
                                            <p v-if="example.summary" class="brief-show-example-description line-clamp-3 text-sm leading-6 text-[#5C6079]">{{ example.summary }}</p>
                                            <p v-else-if="example.location_hint || example.season_hint || example.clothing_hint" class="brief-show-example-description line-clamp-3 text-sm leading-6 text-[#5C6079]">{{ [example.location_hint, example.season_hint, example.clothing_hint].filter((value) => value).join(' / ') }}</p>
                                            <p v-else class="text-sm leading-6 text-[#7A809E]">Matching tags will appear here once filters are assigned.</p>
                                        </div>
                                        <div v-if="example.filter_option_labels.length > 0" class="mt-5 flex flex-wrap gap-2"><Badge v-for="label in example.filter_option_labels.slice(0, 4)" :key="`${example.id}-${label}`" variant="secondary" class="brief-show-chip border border-[#D9DCF3] bg-white px-3 py-1.5 text-[#4252FF]">{{ label }}</Badge></div>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Locations</p>
                                    <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">Recommended locations</h2>
                                </div>
                            </div>

                            <div v-if="locationFilterOptionLabels.length > 0" class="mt-4 flex flex-wrap gap-2">
                                <Badge v-for="label in locationFilterOptionLabels" :key="`location-filter-${label}`" variant="secondary" class="brief-show-chip border border-[#D9DCF3] bg-white px-3 py-1.5 text-[#4252FF]">{{ label }}</Badge>
                            </div>

                            <div v-if="locations.length > 0" class="mt-6 grid gap-5 sm:grid-cols-2">
                                <article v-for="location in locations" :key="location.id" class="brief-show-location-card group overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                    <div class="brief-show-location-accent"></div>
                                    <div class="relative z-10">
                                        <div class="brief-show-location-media rounded-[22px] border-b border-dashed border-black/8 bg-white p-2"><div class="overflow-hidden rounded-[18px] bg-[#E8E8E8]"><img :src="location.image_url" :alt="location.name" class="brief-show-location-image h-[220px] w-full object-cover" loading="lazy" /></div></div>
                                        <div class="mt-4 flex items-start justify-between gap-4">
                                            <div class="flex items-start gap-3">
                                                <div class="brief-show-location-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-white"><MapPinned class="h-5 w-5" /></div>
                                                <div>
                                                    <h3 class="brief-show-location-title font-onest text-[24px] font-medium leading-none text-[#20243B]">{{ location.name }}</h3>
                                                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Location recommendation</p>
                                                </div>
                                            </div>
                                            <div class="brief-show-location-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]"><ArrowRight class="h-4 w-4" /></div>
                                        </div>
                                        <div v-if="location.filter_option_labels.length > 0" class="mt-5 flex flex-wrap gap-2"><Badge v-for="label in location.filter_option_labels.slice(0, 3)" :key="`${location.id}-${label}`" variant="secondary" class="brief-show-chip border border-[#D9DCF3] bg-white px-3 py-1.5 text-[#4252FF]">{{ label }}</Badge></div>
                                    </div>
                                </article>
                            </div>

                            <div v-else class="mt-6 rounded-[28px] border border-dashed border-[#D9DCF3] bg-[#F7F8FF] p-5 text-sm leading-6 text-[#5C6079]">No locations match the selected filters for this brief.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>
