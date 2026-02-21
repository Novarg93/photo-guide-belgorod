<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Check } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
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

interface PresetItem {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    mood: string | null;
    season_hint: string | null;
    location_hint: string | null;
    clothing_hint: string | null;
}

interface ActivePreset {
    slug: string;
    title: string;
    summary: string | null;
}

const props = defineProps<{
    category: Category;
    examples: ExampleItem[];
    presets: PresetItem[];
    activePreset: ActivePreset | null;
    filterOptions: FilterOptions;
    activeFilters: ActiveFilters;
    metaTitle: string;
    metaDescription: string;
}>();

const page = usePage();
const searchParams = new URLSearchParams(page.url.split('?')[1] ?? '');

const selectedMood = ref<string>(props.activeFilters.mood ?? 'all');
const selectedSeason = ref<string>(props.activeFilters.season ?? 'all');
const selectedLocation = ref<string>(props.activeFilters.location ?? 'all');
const selectedClothing = ref<string>(props.activeFilters.clothing ?? 'all');
const selectedPreset = ref<string>(props.activePreset?.slug ?? 'custom');
const explicitFilters = ref({
    mood: searchParams.has('mood'),
    season: searchParams.has('season'),
    location: searchParams.has('location'),
    clothing: searchParams.has('clothing'),
});
const selectedExampleIds = ref<number[]>([]);
const isBriefDialogOpen = ref(false);
const briefPeopleCount = ref<string>('not_set');
const briefNotes = ref<string>('');
const briefRetouchPreference = ref<string>('not_set');
const briefColorStyle = ref<string>('not_set');

const currentPreset = computed(() => {
    if (selectedPreset.value === 'custom') {
        return null;
    }

    return props.presets.find((preset) => preset.slug === selectedPreset.value) ?? null;
});

const presetHelperText = computed(() => {
    if (selectedPreset.value === 'custom') {
        return 'Presets are quick starting points. You can still change filters below.';
    }

    if (currentPreset.value?.summary) {
        return `${currentPreset.value.summary} Filters are pre-filled and can be adjusted below.`;
    }

    return 'This preset pre-fills filters. You can still change filters below.';
});

const buildQuery = (): Record<string, string> => {
    const query: Record<string, string> = {};

    if (selectedPreset.value !== 'custom') {
        query.preset = selectedPreset.value;
    }

    if (explicitFilters.value.mood && selectedMood.value !== 'all') {
        query.mood = selectedMood.value;
    }

    if (explicitFilters.value.season && selectedSeason.value !== 'all') {
        query.season = selectedSeason.value;
    }

    if (explicitFilters.value.location && selectedLocation.value !== 'all') {
        query.location = selectedLocation.value;
    }

    if (explicitFilters.value.clothing && selectedClothing.value !== 'all') {
        query.clothing = selectedClothing.value;
    }

    return query;
};

const applyFilters = (): void => {
    router.visit(showCategory.url({ slug: props.category.slug }, { query: buildQuery() }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const detachPresetOnManualChange = (): void => {
    if (selectedPreset.value === 'custom') {
        return;
    }

    selectedPreset.value = 'custom';
    explicitFilters.value = {
        mood: selectedMood.value !== 'all',
        season: selectedSeason.value !== 'all',
        location: selectedLocation.value !== 'all',
        clothing: selectedClothing.value !== 'all',
    };
};

const updateFilter = (key: 'mood' | 'season' | 'location' | 'clothing', value: string): void => {
    if (key === 'mood') {
        selectedMood.value = value;
        explicitFilters.value.mood = value !== 'all';
    }

    if (key === 'season') {
        selectedSeason.value = value;
        explicitFilters.value.season = value !== 'all';
    }

    if (key === 'location') {
        selectedLocation.value = value;
        explicitFilters.value.location = value !== 'all';
    }

    if (key === 'clothing') {
        selectedClothing.value = value;
        explicitFilters.value.clothing = value !== 'all';
    }

    detachPresetOnManualChange();
    applyFilters();
};

const updatePreset = (value: string): void => {
    selectedPreset.value = value;

    if (value !== 'custom') {
        const preset = props.presets.find((item) => item.slug === value);

        if (preset) {
            if (!explicitFilters.value.mood) {
                selectedMood.value = preset.mood ?? 'all';
            }

            if (!explicitFilters.value.season) {
                selectedSeason.value = preset.season_hint ?? 'all';
            }

            if (!explicitFilters.value.location) {
                selectedLocation.value = preset.location_hint ?? 'all';
            }

            if (!explicitFilters.value.clothing) {
                selectedClothing.value = preset.clothing_hint ?? 'all';
            }
        }
    }

    applyFilters();
};

const clearPreset = (): void => {
    selectedPreset.value = 'custom';

    if (!explicitFilters.value.mood) {
        selectedMood.value = 'all';
    }

    if (!explicitFilters.value.season) {
        selectedSeason.value = 'all';
    }

    if (!explicitFilters.value.location) {
        selectedLocation.value = 'all';
    }

    if (!explicitFilters.value.clothing) {
        selectedClothing.value = 'all';
    }

    applyFilters();
};

const resetFilters = (): void => {
    selectedPreset.value = 'custom';
    selectedMood.value = 'all';
    selectedSeason.value = 'all';
    selectedLocation.value = 'all';
    selectedClothing.value = 'all';
    explicitFilters.value = {
        mood: false,
        season: false,
        location: false,
        clothing: false,
    };

    router.visit(showCategory.url({ slug: props.category.slug }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const createBrief = (): void => {
    const toNullable = (value: string): string | null => (value === 'all' ? null : value);
    const toNullableBriefValue = (value: string): string | null => (value === 'not_set' ? null : value);
    const preparedNotes = briefNotes.value.trim();

    router.post(
        '/briefs',
        {
            category_slug: props.category.slug,
            mood: toNullable(selectedMood.value),
            season: toNullable(selectedSeason.value),
            location: toNullable(selectedLocation.value),
            clothing: toNullable(selectedClothing.value),
            people_count: toNullableBriefValue(briefPeopleCount.value),
            notes: preparedNotes.length > 0 ? preparedNotes : null,
            retouch_preference: toNullableBriefValue(briefRetouchPreference.value),
            color_style: toNullableBriefValue(briefColorStyle.value),
            selected_example_ids: selectedExampleIds.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                isBriefDialogOpen.value = false;
            },
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

const setBriefPeopleCount = (value: string): void => {
    briefPeopleCount.value = value;
};

const setBriefRetouchPreference = (value: string): void => {
    briefRetouchPreference.value = value;
};

const setBriefColorStyle = (value: string): void => {
    briefColorStyle.value = value;
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

            <div class="mt-8 rounded-2xl border border-zinc-200 bg-zinc-50/70 p-4">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                    <div class="space-y-2">
                        <Label for="preset-selector">Example preset</Label>
                        <Select :model-value="selectedPreset" @update:model-value="(value) => updatePreset(String(value))">
                            <SelectTrigger id="preset-selector">
                                <SelectValue placeholder="Custom" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="custom">Custom</SelectItem>
                                <SelectItem v-for="preset in presets" :key="preset.id" :value="preset.slug">
                                    {{ preset.title }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-sm text-zinc-600">{{ presetHelperText }}</p>
                    </div>

                    <Button variant="outline" :disabled="selectedPreset === 'custom'" @click="clearPreset">
                        Clear preset
                    </Button>
                </div>
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
                <Dialog v-model:open="isBriefDialogOpen">
                    <DialogTrigger as-child>
                        <Button>Create brief</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Create brief</DialogTitle>
                            <DialogDescription>
                                Add optional details before generating your brief link.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="space-y-4 py-2">
                            <div class="space-y-2">
                                <Label for="people-count">People count</Label>
                                <Select
                                    :model-value="briefPeopleCount"
                                    @update:model-value="(value) => setBriefPeopleCount(String(value))"
                                >
                                    <SelectTrigger id="people-count">
                                        <SelectValue placeholder="Optional" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="not_set">Not specified</SelectItem>
                                        <SelectItem value="1">1</SelectItem>
                                        <SelectItem value="2">2</SelectItem>
                                        <SelectItem value="3-4">3-4</SelectItem>
                                        <SelectItem value="5+">5+</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="space-y-2">
                                <Label for="brief-notes">Notes</Label>
                                <textarea
                                    id="brief-notes"
                                    v-model="briefNotes"
                                    rows="4"
                                    class="flex w-full rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm shadow-xs outline-none ring-offset-white placeholder:text-zinc-400 focus-visible:ring-2 focus-visible:ring-zinc-900 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Important details, preferences, style, props, retouch notes"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="retouch-preference">Retouch preference</Label>
                                <Select
                                    :model-value="briefRetouchPreference"
                                    @update:model-value="(value) => setBriefRetouchPreference(String(value))"
                                >
                                    <SelectTrigger id="retouch-preference">
                                        <SelectValue placeholder="Optional" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="not_set">Not specified</SelectItem>
                                        <SelectItem value="Natural">Natural</SelectItem>
                                        <SelectItem value="Classic">Classic</SelectItem>
                                        <SelectItem value="Glam">Glam</SelectItem>
                                        <SelectItem value="Photographer decides">Photographer decides</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="space-y-2">
                                <Label for="color-style">Color style</Label>
                                <Select
                                    :model-value="briefColorStyle"
                                    @update:model-value="(value) => setBriefColorStyle(String(value))"
                                >
                                    <SelectTrigger id="color-style">
                                        <SelectValue placeholder="Optional" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="not_set">Not specified</SelectItem>
                                        <SelectItem value="Warm">Warm</SelectItem>
                                        <SelectItem value="Cool">Cool</SelectItem>
                                        <SelectItem value="Neutral">Neutral</SelectItem>
                                        <SelectItem value="Film">Film</SelectItem>
                                        <SelectItem value="Not sure">Not sure</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <DialogFooter class="gap-2">
                            <Button variant="outline" @click="isBriefDialogOpen = false">Cancel</Button>
                            <Button @click="createBrief">Generate link</Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
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
