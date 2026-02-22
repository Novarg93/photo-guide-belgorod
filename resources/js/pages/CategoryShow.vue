<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
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

interface FilterOption {
    key: string;
    label: string;
}

interface FilterGroup {
    key: string;
    label: string;
    options: FilterOption[];
}

interface ExampleItem {
    id: number;
    title: string;
    summary: string | null;
    mood: string | null;
    location_hint: string | null;
    season_hint: string | null;
    clothing_hint: string | null;
    filter_option_labels: string[];
    image_url: string;
}

interface PresetItem {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    filter_option_keys: string[];
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
    filterGroups: FilterGroup[];
    activeFilterOptionKeys: string[];
    metaTitle: string;
    metaDescription: string;
}>();

const selectedPreset = ref<string>(props.activePreset?.slug ?? 'custom');
const selectedFilterOptionKeys = ref<string[]>([...props.activeFilterOptionKeys]);
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
        return 'Custom preset is active. Select a preset to apply saved filter combinations.';
    }

    if (currentPreset.value?.summary) {
        return currentPreset.value.summary;
    }

    return 'Preset applies saved filters. Manual filter change switches to Custom.';
});

const buildQuery = (): Record<string, string | string[]> => {
    const query: Record<string, string | string[]> = {};

    if (selectedPreset.value !== 'custom') {
        query.preset = selectedPreset.value;
    }

    if (selectedFilterOptionKeys.value.length > 0) {
        query.filters = selectedFilterOptionKeys.value;
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

const toggleFilter = (optionKey: string): void => {
    if (selectedFilterOptionKeys.value.includes(optionKey)) {
        selectedFilterOptionKeys.value = selectedFilterOptionKeys.value.filter((key) => key !== optionKey);
    } else {
        selectedFilterOptionKeys.value = [...selectedFilterOptionKeys.value, optionKey];
    }

    if (selectedPreset.value !== 'custom') {
        selectedPreset.value = 'custom';
    }

    applyFilters();
};

const isFilterSelected = (optionKey: string): boolean => {
    return selectedFilterOptionKeys.value.includes(optionKey);
};

const resetFilters = (): void => {
    selectedPreset.value = 'custom';
    selectedFilterOptionKeys.value = [];

    router.visit(showCategory.url({ slug: props.category.slug }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const updatePreset = (value: string): void => {
    selectedPreset.value = value;

    if (value === 'custom') {
        applyFilters();

        return;
    }

    const preset = props.presets.find((item) => item.slug === value);

    if (! preset) {
        selectedPreset.value = 'custom';
        applyFilters();

        return;
    }

    selectedFilterOptionKeys.value = [...preset.filter_option_keys];
    applyFilters();
};

const createBrief = (): void => {
    const toNullableBriefValue = (value: string): string | null => (value === 'not_set' ? null : value);
    const preparedNotes = briefNotes.value.trim();
    const exampleIdsForBrief =
        selectedExampleIds.value.length > 0 ? selectedExampleIds.value : props.examples.map((example) => example.id);

    router.post(
        '/briefs',
        {
            category_slug: props.category.slug,
            mood: null,
            season: null,
            location: null,
            clothing: null,
            people_count: toNullableBriefValue(briefPeopleCount.value),
            notes: preparedNotes.length > 0 ? preparedNotes : null,
            retouch_preference: toNullableBriefValue(briefRetouchPreference.value),
            color_style: toNullableBriefValue(briefColorStyle.value),
            selected_example_ids: exampleIdsForBrief,
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
                <div class="grid gap-3 border-b border-zinc-200 pb-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                    <div class="space-y-2">
                        <Label for="preset-selector">Preset</Label>
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
                </div>

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-3">
                        <h2 class="pt-4 text-sm font-semibold uppercase tracking-wide text-zinc-500">Filters</h2>

                        <div v-if="filterGroups.length > 0" class="space-y-3">
                            <div v-for="group in filterGroups" :key="group.key" class="space-y-2">
                                <p class="text-sm font-medium text-zinc-800">{{ group.label }}</p>

                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="option in group.options"
                                        :key="option.key"
                                        type="button"
                                        class="rounded-full border px-3 py-1.5 text-sm transition"
                                        :class="[
                                            isFilterSelected(option.key)
                                                ? 'border-zinc-900 bg-zinc-900 text-white'
                                                : 'border-zinc-300 bg-white text-zinc-700 hover:border-zinc-500',
                                        ]"
                                        @click="toggleFilter(option.key)"
                                    >
                                        {{ option.label }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p v-else class="text-sm text-zinc-600">No filter groups configured for this category yet.</p>
                    </div>

                    <Button variant="outline" @click="resetFilters">Reset</Button>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
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

                    <div class="absolute left-3 top-3 flex max-w-[80%] flex-wrap gap-1.5">
                        <Badge
                            v-for="label in example.filter_option_labels.slice(0, 3)"
                            :key="`${example.id}-${label}`"
                            class="bg-white/90 text-zinc-900"
                        >
                            {{ label }}
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
