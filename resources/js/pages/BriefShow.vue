<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { catalog } from '@/routes';
import { show as showCategory } from '@/routes/categories';

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
}>();

const copied = ref(false);

const selectedFilterBadges = computed(() => {
    const badges: Array<{ label: string; value: string }> = [];

    if (props.filters.mood) {
        badges.push({ label: 'Mood', value: props.filters.mood });
    }

    if (props.filters.season) {
        badges.push({ label: 'Season', value: props.filters.season });
    }

    if (props.filters.location) {
        badges.push({ label: 'Location', value: props.filters.location });
    }

    if (props.filters.clothing) {
        badges.push({ label: 'Clothing', value: props.filters.clothing });
    }

    return badges;
});

const backToCategoryUrl = computed(() => {
    const query: Record<string, string | string[]> = {};
    const activeFilterOptionKeys = props.filters.active_filter_option_keys ?? [];

    if (activeFilterOptionKeys.length > 0) {
        query.filters = activeFilterOptionKeys;
    }

    return showCategory.url({ slug: props.category.slug }, { query });
});

const briefText = computed(() => {
    const lines: string[] = [
        'Brief for photographer',
        `Category: ${props.category.name}`,
    ];

    if (selectedFilterBadges.value.length > 0) {
        lines.push('Filters:');

        selectedFilterBadges.value.forEach((badge) => {
            lines.push(`- ${badge.label}: ${badge.value}`);
        });
    } else {
        lines.push('Filters: not selected');
    }

    if (props.peopleCount) {
        lines.push(`People count: ${props.peopleCount}`);
    }

    if (props.notes) {
        lines.push(`Notes: ${props.notes}`);
    }

    if (props.retouchPreference) {
        lines.push(`Retouch: ${props.retouchPreference}`);
    }

    if (props.colorStyle) {
        lines.push(`Color: ${props.colorStyle}`);
    }

    lines.push(`Link: ${props.shareUrl}`);
    lines.push('Examples:');

    if (props.examples.length === 0) {
        lines.push('1. No matching examples');
    } else {
        props.examples.forEach((example, index) => {
            const hints: string[] = [];

            if (example.mood) {
                hints.push(`mood: ${example.mood}`);
            }

            if (example.location_hint) {
                hints.push(`location: ${example.location_hint}`);
            }

            lines.push(
                `${index + 1}. ${example.title}${hints.length > 0 ? ` (${hints.join(', ')})` : ''}`,
            );
        });
    }

    lines.push('Locations:');
    if (props.locationFilterOptionLabels.length > 0) {
        lines.push(`Applied location filters: ${props.locationFilterOptionLabels.join(', ')}`);
    }

    if (props.locations.length === 0) {
        lines.push('1. No matching locations');
    } else {
        props.locations.forEach((location, index) => {
            lines.push(`${index + 1}. ${location.name}`);
        });
    }

    return lines.join('\n');
});

const copyLink = async (): Promise<void> => {
    await navigator.clipboard.writeText(props.shareUrl);
    copied.value = true;

    window.setTimeout(() => {
        copied.value = false;
    }, 1500);
};

const copyBriefText = async (): Promise<void> => {
    await navigator.clipboard.writeText(briefText.value);
    copied.value = true;

    window.setTimeout(() => {
        copied.value = false;
    }, 1500);
};
</script>

<template>
    <AppHeaderLayout>
        <Head :title="metaTitle">
            <meta head-key="description" name="description" :content="metaDescription" />
        </Head>

        <section class="mx-auto w-full max-w-7xl py-8">
            <Link :href="catalog()" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to catalog</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                Brief for photographer
            </h1>

            <h2 class="mt-3 text-xl font-semibold text-zinc-900">
                {{ category.name }}
            </h2>

            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-zinc-600">
                {{ category.description || 'Category description will be added later.' }}
            </p>

            <div class="mt-4 flex flex-wrap items-center gap-2" v-if="selectedFilterBadges.length > 0">
                <Badge v-for="badge in selectedFilterBadges" :key="badge.label" variant="outline">
                    {{ badge.label }}: {{ badge.value }}
                </Badge>
            </div>

            <section
                v-if="peopleCount || notes || retouchPreference || colorStyle"
                class="mt-6 rounded-xl border border-zinc-200 bg-zinc-50 p-4"
            >
                <h3 class="text-sm font-semibold text-zinc-900">Details</h3>
                <p v-if="peopleCount" class="mt-2 text-sm text-zinc-700">People count: {{ peopleCount }}</p>
                <p v-if="notes" class="mt-2 whitespace-pre-line text-sm text-zinc-700">{{ notes }}</p>
                <p v-if="retouchPreference" class="mt-2 text-sm text-zinc-700">Retouch preference: {{ retouchPreference }}</p>
                <p v-if="colorStyle" class="mt-2 text-sm text-zinc-700">Color style: {{ colorStyle }}</p>
            </section>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <Button @click="copyLink">Copy link</Button>
                <Button variant="outline" @click="copyBriefText">Copy brief text</Button>
                <span v-if="copied" class="text-sm text-green-600">Copied</span>
                <p class="max-w-full truncate text-sm text-zinc-500">{{ shareUrl }}</p>
            </div>

            <p class="mt-2 text-sm text-zinc-600">Send this link to your photographer</p>

            <div class="mt-4">
                <Link :href="backToCategoryUrl" class="text-sm font-medium text-zinc-700 underline underline-offset-4">
                    Back to category
                </Link>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="example in examples"
                    :key="example.id"
                    class="group overflow-hidden rounded-2xl border border-zinc-200 bg-white"
                >
                    <div class="aspect-16/10 overflow-hidden bg-zinc-100">
                        <img
                            :src="example.image_url"
                            :alt="example.title"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                    </div>

                    <div class="space-y-3 p-4">
                        <div class="space-y-2">
                            <h2 class="line-clamp-2 text-lg font-semibold text-zinc-900">{{ example.title }}</h2>

                            <p v-if="example.summary" class="line-clamp-2 text-sm leading-relaxed text-zinc-600">
                                {{ example.summary }}
                            </p>

                            <p
                                v-else-if="example.location_hint || example.season_hint || example.clothing_hint"
                                class="line-clamp-2 text-sm leading-relaxed text-zinc-600"
                            >
                                {{
                                    [example.location_hint, example.season_hint, example.clothing_hint]
                                        .filter((value) => value)
                                        .join(' / ')
                                }}
                            </p>
                        </div>

                        <div v-if="example.filter_option_labels.length > 0" class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="label in example.filter_option_labels.slice(0, 4)"
                                :key="`${example.id}-${label}`"
                                variant="secondary"
                                class="bg-zinc-100 text-zinc-800"
                            >
                                {{ label }}
                            </Badge>
                        </div>

                        <p v-else class="text-sm text-zinc-500">
                            Matching tags will appear here once filters are assigned.
                        </p>
                    </div>
                </article>
            </div>

            <div class="mt-12 flex items-center justify-between gap-4">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">Recommended locations</h2>
            </div>

            <div v-if="locationFilterOptionLabels.length > 0" class="mt-3 flex flex-wrap gap-1.5">
                <Badge
                    v-for="label in locationFilterOptionLabels"
                    :key="`location-filter-${label}`"
                    variant="outline"
                >
                    {{ label }}
                </Badge>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="location in locations"
                    :key="location.id"
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white"
                >
                    <div class="aspect-16/10 overflow-hidden bg-zinc-100">
                        <img
                            :src="location.image_url"
                            :alt="location.name"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                    </div>

                    <div class="space-y-2 p-4">
                        <h3 class="text-base font-semibold text-zinc-900">{{ location.name }}</h3>

                        <div class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="label in location.filter_option_labels.slice(0, 3)"
                                :key="`${location.id}-${label}`"
                                variant="secondary"
                                class="bg-zinc-100 text-zinc-800"
                            >
                                {{ label }}
                            </Badge>
                        </div>
                    </div>
                </article>
            </div>

            <div
                v-if="locations.length === 0"
                class="mt-4 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600"
            >
                No locations match the selected filters for this brief.
            </div>
        </section>
    </AppHeaderLayout>
</template>
