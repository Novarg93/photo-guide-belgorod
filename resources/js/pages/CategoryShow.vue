<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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

interface Category {
    name: string;
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

const props = defineProps<{
    category: Category;
    examples: ExampleItem[];
    metaTitle: string;
    metaDescription: string;
}>();

const selectedMood = ref<string>('all');
const selectedSeason = ref<string>('all');
const selectedLocation = ref<string>('all');
const selectedClothing = ref<string>('all');

const collectOptions = (key: keyof ExampleItem): string[] => {
    return [...new Set(props.examples.map((example) => example[key]).filter((value): value is string => Boolean(value)))].sort();
};

const moodOptions = computed(() => collectOptions('mood'));
const seasonOptions = computed(() => collectOptions('season_hint'));
const locationOptions = computed(() => collectOptions('location_hint'));
const clothingOptions = computed(() => collectOptions('clothing_hint'));

const filteredExamples = computed(() => {
    return props.examples.filter((example) => {
        if (selectedMood.value !== 'all' && example.mood !== selectedMood.value) {
            return false;
        }

        if (selectedSeason.value !== 'all' && example.season_hint !== selectedSeason.value) {
            return false;
        }

        if (selectedLocation.value !== 'all' && example.location_hint !== selectedLocation.value) {
            return false;
        }

        if (selectedClothing.value !== 'all' && example.clothing_hint !== selectedClothing.value) {
            return false;
        }

        return true;
    });
});

const resetFilters = (): void => {
    selectedMood.value = 'all';
    selectedSeason.value = 'all';
    selectedLocation.value = 'all';
    selectedClothing.value = 'all';
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

                <p class="text-sm text-zinc-500">{{ filteredExamples.length }} results</p>
            </div>

            <div class="mt-8 grid gap-3 rounded-2xl border border-zinc-200 bg-zinc-50/70 p-4 md:grid-cols-5">
                <Select v-model="selectedMood">
                    <SelectTrigger>
                        <SelectValue placeholder="Mood" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All moods</SelectItem>
                        <SelectItem v-for="option in moodOptions" :key="`mood-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="selectedSeason">
                    <SelectTrigger>
                        <SelectValue placeholder="Season" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All seasons</SelectItem>
                        <SelectItem v-for="option in seasonOptions" :key="`season-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="selectedLocation">
                    <SelectTrigger>
                        <SelectValue placeholder="Location" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All locations</SelectItem>
                        <SelectItem v-for="option in locationOptions" :key="`location-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="selectedClothing">
                    <SelectTrigger>
                        <SelectValue placeholder="Clothing" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All clothing</SelectItem>
                        <SelectItem v-for="option in clothingOptions" :key="`clothing-${option}`" :value="option">{{ option }}</SelectItem>
                    </SelectContent>
                </Select>

                <Button variant="outline" @click="resetFilters">Reset</Button>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="example in filteredExamples"
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
                        <h2 class="line-clamp-2 text-lg font-semibold text-white">
                            {{ example.title }}
                        </h2>
                    </div>
                </article>
            </div>

            <div
                v-if="filteredExamples.length === 0"
                class="mt-8 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600"
            >
                No examples match the selected filters.
            </div>
        </section>
    </AppHeaderLayout>
</template>
