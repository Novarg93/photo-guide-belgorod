<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
}

interface ExampleItem {
    id: number;
    title: string;
    summary: string | null;
    mood: string | null;
    location_hint: string | null;
    season_hint: string | null;
    clothing_hint: string | null;
    image_url: string;
}

const props = defineProps<{
    category: Category;
    filters: Filters;
    examples: ExampleItem[];
    selectedExampleIds: number[];
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
    const query: Record<string, string> = {};

    if (props.filters.mood) {
        query.mood = props.filters.mood;
    }

    if (props.filters.season) {
        query.season = props.filters.season;
    }

    if (props.filters.location) {
        query.location = props.filters.location;
    }

    if (props.filters.clothing) {
        query.clothing = props.filters.clothing;
    }

    return showCategory.url({ slug: props.category.slug }, { query });
});

const copyLink = async (): Promise<void> => {
    await navigator.clipboard.writeText(props.shareUrl);
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

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <Button @click="copyLink">Copy link</Button>
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
                    class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100"
                >
                    <div class="aspect-4/5 overflow-hidden">
                        <img
                            :src="example.image_url"
                            :alt="example.title"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                    </div>

                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>

                    <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                        <Badge v-if="example.mood" class="bg-white/90 text-zinc-900">{{ example.mood }}</Badge>
                        <Badge v-if="example.location_hint" variant="secondary" class="bg-white/80 text-zinc-900">
                            {{ example.location_hint }}
                        </Badge>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 p-4">
                        <h2 class="line-clamp-2 text-lg font-semibold text-white">{{ example.title }}</h2>
                    </div>
                </article>
            </div>
        </section>
    </AppHeaderLayout>
</template>
