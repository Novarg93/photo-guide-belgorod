<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Check } from 'lucide-vue-next';
import { ref } from 'vue';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { catalog } from '@/routes';
import { show as showCategory } from '@/routes/categories';

interface Category {
    name: string;
    slug: string;
    description: string | null;
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

interface FilterOptions {
    moods: string[];
    seasons: string[];
    locations: string[];
    clothings: string[];
}

interface ActiveFilters {
    mood: string | null;
    season: string | null;
    location: string | null;
    clothing: string | null;
}

const props = defineProps<{
    category: Category;
    examples: ExampleItem[];
    filterOptions: FilterOptions;
    activeFilters: ActiveFilters;
    metaTitle: string;
    metaDescription: string;
}>();

const selectedMood = ref<string>(props.activeFilters.mood ?? 'all');
const selectedSeason = ref<string>(props.activeFilters.season ?? 'all');
const selectedLocation = ref<string>(props.activeFilters.location ?? 'all');
const selectedClothing = ref<string>(props.activeFilters.clothing ?? 'all');
const selectedExampleIds = ref<number[]>([]);

const applyFilters = (): void => {
    const query: Record<string, string> = {};

    if (selectedMood.value !== 'all') {
        query.mood = selectedMood.value;
    }

    if (selectedSeason.value !== 'all') {
        query.season = selectedSeason.value;
    }

    if (selectedLocation.value !== 'all') {
        query.location = selectedLocation.value;
    }

    if (selectedClothing.value !== 'all') {
        query.clothing = selectedClothing.value;
    }

    router.visit(showCategory.url({ slug: props.category.slug }, { query }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const updateFilter = (key: 'mood' | 'season' | 'location' | 'clothing', value: string): void => {
    if (key === 'mood') {
        selectedMood.value = value;
    }

    if (key === 'season') {
        selectedSeason.value = value;
    }

    if (key === 'location') {
        selectedLocation.value = value;
    }

    if (key === 'clothing') {
        selectedClothing.value = value;
    }

    applyFilters();
};

const resetFilters = (): void => {
    selectedMood.value = 'all';
    selectedSeason.value = 'all';
    selectedLocation.value = 'all';
    selectedClothing.value = 'all';

    router.visit(showCategory.url({ slug: props.category.slug }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const createBrief = (): void => {
    const toNullable = (value: string): string | null => (value === 'all' ? null : value);

    router.post(
        '/briefs',
        {
            category_slug: props.category.slug,
            mood: toNullable(selectedMood.value),
            season: toNullable(selectedSeason.value),
            location: toNullable(selectedLocation.value),
            clothing: toNullable(selectedClothing.value),
            selected_example_ids: selectedExampleIds.value,
        },
        {
            preserveScroll: true,
        },
    );
};

const toggleExampleSelection = (exampleId: number): void => {
    if (selectedExampleIds.value.includes(exampleId)) {
        selectedExampleIds.value = selectedExampleIds.value.filter((id) => id !== exampleId);

        return;
    }

    selectedExampleIds.value = [...selectedExampleIds.value, exampleId];
};

const isExampleSelected = (exampleId: number): boolean => {
    return selectedExampleIds.value.includes(exampleId);
};
</script>

<template>
    <AppHeaderLayout>
        <Head :title="metaTitle">
            <meta head-key="description" name="description" :content="metaDescription" />
        </Head>

        <section class="mx-auto w-full max-w-7xl py-8">
            <Link :href="catalog()" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to catalog</Link>

            <div class="mt-4 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                        {{ category.name }}
                    </h1>

                    <p class="mt-3 max-w-3xl text-sm leading-relaxed text-zinc-600">
                        {{ category.description || 'Category description will be added later.' }}
                    </p>
                </div>

                <p class="text-sm text-zinc-500">{{ examples.length }} results</p>
            </div>

            <div class="mt-8 grid gap-3 rounded-2xl border border-zinc-200 bg-zinc-50/70 p-4 md:grid-cols-6">
                <Select :model-value="selectedMood" @update:model-value="(value) => updateFilter('mood', String(value))">
                    <SelectTrigger>
                        <SelectValue placeholder="Mood" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All moods</SelectItem>
                        <SelectItem v-for="option in filterOptions.moods" :key="`mood-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Select :model-value="selectedSeason" @update:model-value="(value) => updateFilter('season', String(value))">
                    <SelectTrigger>
                        <SelectValue placeholder="Season" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All seasons</SelectItem>
                        <SelectItem v-for="option in filterOptions.seasons" :key="`season-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Select :model-value="selectedLocation" @update:model-value="(value) => updateFilter('location', String(value))">
                    <SelectTrigger>
                        <SelectValue placeholder="Location" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All locations</SelectItem>
                        <SelectItem v-for="option in filterOptions.locations" :key="`location-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Select :model-value="selectedClothing" @update:model-value="(value) => updateFilter('clothing', String(value))">
                    <SelectTrigger>
                        <SelectValue placeholder="Clothing" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All clothing</SelectItem>
                        <SelectItem v-for="option in filterOptions.clothings" :key="`clothing-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Button variant="outline" @click="resetFilters">Reset</Button>
                <Button @click="createBrief">Create brief</Button>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="example in examples"
                    :key="example.id"
                    class="group relative cursor-pointer overflow-hidden rounded-2xl border bg-zinc-100"
                    :class="[
                        isExampleSelected(example.id)
                            ? 'border-zinc-900 ring-2 ring-zinc-900/60'
                            : 'border-zinc-200',
                    ]"
                    @click="toggleExampleSelection(example.id)"
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

                    <div
                        v-if="isExampleSelected(example.id)"
                        class="absolute right-3 top-3 rounded-full bg-zinc-900 p-1.5 text-white shadow-md"
                    >
                        <Check class="h-4 w-4" />
                    </div>

                    <div class="absolute inset-x-0 bottom-0 p-4">
                        <h2 class="line-clamp-2 text-lg font-semibold text-white">
                            {{ example.title }}
                        </h2>
                    </div>
                </article>
            </div>

            <div
                v-if="examples.length === 0"
                class="mt-8 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600"
            >
                No examples match the selected filters.
            </div>
        </section>
    </AppHeaderLayout>
</template>
