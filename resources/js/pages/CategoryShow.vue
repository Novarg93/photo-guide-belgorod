<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Check, MapPinned, SlidersHorizontal, Sparkles } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'
import { catalog } from '@/routes'
import { show as showCategory } from '@/routes/categories'

interface Category {
    name: string;
    title: string | null;
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
    id: string;
    example_id: number | null;
    photo_id: number | null;
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
    locations: LocationItem[];
    presets: PresetItem[];
    activePreset: ActivePreset | null;
    filterGroups: FilterGroup[];
    activeFilterOptionKeys: string[];
    metaTitle: string;
    metaDescription: string;
}>()

useHead(() => ({
    title: props.metaTitle,
    meta: [
        { key: 'description', name: 'description', content: props.metaDescription },
    ],
}))

const selectedPreset = ref<string>(props.activePreset?.slug ?? 'custom')
const selectedFilterOptionKeys = ref<string[]>([...props.activeFilterOptionKeys])
const selectedCardIds = ref<string[]>([])
const isBriefDialogOpen = ref(false)
const briefPeopleCount = ref<string>('not_set')
const briefNotes = ref<string>('')
const briefRetouchPreference = ref<string>('not_set')
const briefColorStyle = ref<string>('not_set')

const currentPreset = computed(() => {
    if (selectedPreset.value === 'custom') {
        return null
    }

    return props.presets.find((preset) => preset.slug === selectedPreset.value) ?? null
})

const presetHelperText = computed(() => {
    if (selectedPreset.value === 'custom') {
        return 'Сейчас активен пользовательский пресет. Выберите пресет, чтобы применить сохраненную комбинацию фильтров.'
    }

    if (currentPreset.value?.summary) {
        return currentPreset.value.summary
    }

    return 'Пресет применяет сохраненные фильтры. При ручном изменении фильтров режим переключится на «Пользовательский».'
})

const buildQuery = (): Record<string, string | string[]> => {
    const query: Record<string, string | string[]> = {}

    if (selectedPreset.value !== 'custom') {
        query.preset = selectedPreset.value
    }

    if (selectedFilterOptionKeys.value.length > 0) {
        query.filters = selectedFilterOptionKeys.value
    }

    return query
}

const applyFilters = (): void => {
    router.visit(showCategory.url({ slug: props.category.slug }, { query: buildQuery() }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const toggleFilter = (optionKey: string): void => {
    if (selectedFilterOptionKeys.value.includes(optionKey)) {
        selectedFilterOptionKeys.value = selectedFilterOptionKeys.value.filter((key) => key !== optionKey)
    } else {
        selectedFilterOptionKeys.value = [...selectedFilterOptionKeys.value, optionKey]
    }

    if (selectedPreset.value !== 'custom') {
        selectedPreset.value = 'custom'
    }

    applyFilters()
}

const isFilterSelected = (optionKey: string): boolean => {
    return selectedFilterOptionKeys.value.includes(optionKey)
}

const resetFilters = (): void => {
    selectedPreset.value = 'custom'
    selectedFilterOptionKeys.value = []

    router.visit(showCategory.url({ slug: props.category.slug }), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const updatePreset = (value: string): void => {
    selectedPreset.value = value

    if (value === 'custom') {
        applyFilters()

        return
    }

    const preset = props.presets.find((item) => item.slug === value)

    if (!preset) {
        selectedPreset.value = 'custom'
        applyFilters()

        return
    }

    selectedFilterOptionKeys.value = [...preset.filter_option_keys]
    applyFilters()
}

const createBrief = (): void => {
    const toNullableBriefValue = (value: string): string | null => (value === 'not_set' ? null : value)
    const preparedNotes = briefNotes.value.trim()
    const selectedCards = selectedCardIds.value.length > 0
        ? props.examples.filter((example) => selectedCardIds.value.includes(example.id))
        : props.examples

    const exampleIdsForBrief = Array.from(new Set(selectedCards.map((example) => example.example_id).filter((exampleId): exampleId is number => exampleId !== null)))
    const photoIdsForBrief = Array.from(new Set(selectedCards.map((example) => example.photo_id).filter((photoId): photoId is number => photoId !== null)))

    router.post('/briefs', {
        category_slug: props.category.slug,
        mood: null,
        season: null,
        location: null,
        clothing: null,
        people_count: toNullableBriefValue(briefPeopleCount.value),
        notes: preparedNotes.length > 0 ? preparedNotes : null,
        retouch_preference: toNullableBriefValue(briefRetouchPreference.value),
        color_style: toNullableBriefValue(briefColorStyle.value),
        active_filter_option_keys: selectedFilterOptionKeys.value,
        selected_example_ids: exampleIdsForBrief,
        selected_photo_ids: photoIdsForBrief,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isBriefDialogOpen.value = false
        },
    })
}

const toggleExampleSelection = (cardId: string): void => {
    if (selectedCardIds.value.includes(cardId)) {
        selectedCardIds.value = selectedCardIds.value.filter((id) => id !== cardId)
        return
    }

    selectedCardIds.value = [...selectedCardIds.value, cardId]
}

const isExampleSelected = (cardId: string): boolean => {
    return selectedCardIds.value.includes(cardId)
}

const setBriefPeopleCount = (value: string): void => {
    briefPeopleCount.value = value
}

const setBriefRetouchPreference = (value: string): void => {
    briefRetouchPreference.value = value
}

const setBriefColorStyle = (value: string): void => {
    briefColorStyle.value = value
}
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
                        <Link :href="catalog()" class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>Назад в каталог</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-end">
                            <div>
                                <h1 class="text-[#20243B]">
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">{{ category.title || category.name }}</span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">черновик</span>
                                </h1>
                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">{{ category.description || 'Описание категории появится позже.' }}</p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-[20px] bg-[#F7F8FF] px-4 py-4">
                                        <p class="font-onest text-sm font-medium text-[#A0A3B8]">Подходящие примеры</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none text-[#20243B]">{{ examples.length }}</span>
                                            <span class="pb-1 text-sm text-[#5C6079]">карточек доступно</span>
                                        </div>
                                    </div>

                                    <div class="rounded-[20px] bg-[#20243B] px-4 py-4 text-white">
                                        <p class="font-onest text-sm font-medium text-white/60">Рекомендуемые локации</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none">{{ locations.length }}</span>
                                            <span class="pb-1 text-sm text-white/70">мест привязано</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-[18px] border border-[#E2E5F6] bg-white px-4 py-3 text-sm leading-6 text-[#303651]">Начните с пресета, уточните фильтры, выберите самые сильные референсы и затем создайте бриф на основе нужного вам визуального направления.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[0.34fr_0.66fr]">
                    <aside class="space-y-5">
                        <div class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Работа с пресетами</p>
                                    <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">Фильтры и пресеты</h2>
                                </div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-[#F7F8FF] text-[#4252FF]"><SlidersHorizontal class="h-5 w-5" /></div>
                            </div>

                            <div class="mt-6 rounded-[24px] bg-[#F7F8FF] p-4">
                                <div class="space-y-2">
                                    <Label for="preset-selector" class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Пресет</Label>
                                    <Select :model-value="selectedPreset" @update:model-value="(value) => updatePreset(String(value))">
                                        <SelectTrigger id="preset-selector" class="category-show-select"><SelectValue placeholder="Пользовательский" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="custom">Пользовательский</SelectItem>
                                            <SelectItem v-for="preset in presets" :key="preset.id" :value="preset.slug">{{ preset.title }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p class="text-sm leading-6 text-[#5C6079]">{{ presetHelperText }}</p>
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Группы фильтров</p>
                                    <p class="mt-2 text-sm leading-6 text-[#5C6079]">Переключайте чипы, чтобы сузить визуальное направление. При ручном изменении фильтров пресет всегда переключается обратно на пользовательский.</p>
                                </div>
                                <Button variant="outline" class="category-show-reset" @click="resetFilters">Сбросить</Button>
                            </div>

                            <div v-if="filterGroups.length > 0" class="mt-6 space-y-4">
                                <div v-for="group in filterGroups" :key="group.key" class="rounded-[24px] border border-[#E2E5F6] bg-[#FCFCFF] p-4">
                                    <p class="text-sm font-medium text-[#20243B]">{{ group.label }}</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button v-for="option in group.options" :key="option.key" type="button" class="category-show-filter-chip rounded-full border px-3 py-2 text-sm transition" :class="[isFilterSelected(option.key) ? 'border-[#20243B] bg-[#20243B] text-white' : 'border-[#D9DCF3] bg-white text-[#5C6079]']" @click="toggleFilter(option.key)">{{ option.label }}</button>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="mt-6 rounded-[24px] border border-dashed border-[#D9DCF3] bg-[#F7F8FF] p-4 text-sm leading-6 text-[#5C6079]">Для этой категории группы фильтров пока не настроены.</div>
                        </div>

                        <Dialog v-model:open="isBriefDialogOpen">
                            <DialogTrigger as-child>
                                <button class="category-show-brief-card block w-full rounded-[32px] bg-[#20243B] p-5 text-left text-white shadow-[0px_18px_40px_rgba(20,23,45,0.14)]">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Конструктор брифа</p>
                                    <p class="mt-4 font-onest text-[28px] font-medium leading-none">Создать бриф</p>
                                    <p class="mt-4 max-w-sm text-sm leading-6 text-white/72">Сгенерируйте ссылку на бриф на основе всех отфильтрованных референсов или только тех карточек, которые вы выберете ниже.</p>
                                    <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-white/14 bg-white/8 px-4 py-2 text-sm font-medium text-white"><span>Открыть окно</span><ArrowRight class="category-show-brief-arrow h-4 w-4" /></div>
                                </button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Создать бриф</DialogTitle>
                                    <DialogDescription>Добавьте необязательные детали перед генерацией ссылки на бриф.</DialogDescription>
                                </DialogHeader>
                                <div class="space-y-4 py-2">
                                    <div class="space-y-2">
                                        <Label for="people-count">Количество людей</Label>
                                        <Select :model-value="briefPeopleCount" @update:model-value="(value) => setBriefPeopleCount(String(value))">
                                            <SelectTrigger id="people-count"><SelectValue placeholder="Необязательно" /></SelectTrigger>
                                            <SelectContent><SelectItem value="not_set">Не указано</SelectItem><SelectItem value="1">1</SelectItem><SelectItem value="2">2</SelectItem><SelectItem value="3-4">3-4</SelectItem><SelectItem value="5+">5+</SelectItem></SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="brief-notes">Заметки</Label>
                                        <textarea id="brief-notes" v-model="briefNotes" rows="4" class="flex w-full rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm shadow-xs outline-none ring-offset-white placeholder:text-zinc-400 focus-visible:ring-2 focus-visible:ring-zinc-900 disabled:cursor-not-allowed disabled:opacity-50" placeholder="Важные детали, предпочтения, стиль, реквизит, пожелания по ретуши" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="retouch-preference">Предпочтения по ретуши</Label>
                                        <Select :model-value="briefRetouchPreference" @update:model-value="(value) => setBriefRetouchPreference(String(value))">
                                            <SelectTrigger id="retouch-preference"><SelectValue placeholder="Необязательно" /></SelectTrigger>
                                            <SelectContent><SelectItem value="not_set">Не указано</SelectItem><SelectItem value="Natural">Естественная</SelectItem><SelectItem value="Classic">Классическая</SelectItem><SelectItem value="Glam">Гламур</SelectItem><SelectItem value="Photographer decides">На усмотрение фотографа</SelectItem></SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="color-style">Цветовой стиль</Label>
                                        <Select :model-value="briefColorStyle" @update:model-value="(value) => setBriefColorStyle(String(value))">
                                            <SelectTrigger id="color-style"><SelectValue placeholder="Необязательно" /></SelectTrigger>
                                            <SelectContent><SelectItem value="not_set">Не указано</SelectItem><SelectItem value="Warm">Теплый</SelectItem><SelectItem value="Cool">Холодный</SelectItem><SelectItem value="Neutral">Нейтральный</SelectItem><SelectItem value="Film">Пленочный</SelectItem><SelectItem value="Not sure">Не уверен(а)</SelectItem></SelectContent>
                                        </Select>
                                    </div>
                                </div>
                                <DialogFooter class="gap-2"><Button variant="outline" @click="isBriefDialogOpen = false">Отмена</Button><Button @click="createBrief">Сгенерировать ссылку</Button></DialogFooter>
                            </DialogContent>
                        </Dialog>

                        <div class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Состояние выбора</p>
                            <p class="mt-3 font-onest text-[24px] font-medium leading-none text-[#20243B]">{{ selectedCardIds.length > 0 ? `${selectedCardIds.length} карточек выбрано` : 'По умолчанию будут включены все карточки' }}</p>
                            <p class="mt-4 text-sm leading-6 text-[#5C6079]">Если вы не выберете конкретные карточки, генератор брифа использует все примеры, которые сейчас видны в этой категории.</p>
                        </div>
                    </aside>

                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Примеры</p>
                                    <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">Выберите визуальные референсы</h2>
                                </div>
                                <p class="text-sm text-[#5C6079]">{{ examples.length }} результатов</p>
                            </div>

                            <div v-if="examples.length > 0" class="mt-6 grid gap-5 sm:grid-cols-2">
                                <article v-for="example in examples" :key="example.id" class="category-show-example-card group relative cursor-pointer overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]" :class="[isExampleSelected(example.id) ? 'category-show-example-card-selected' : '']" @click="toggleExampleSelection(example.id)">
                                    <div class="category-show-example-accent"></div>
                                    <div class="relative z-10">
                                        <div class="category-show-example-media rounded-[22px] border-b border-dashed border-black/8 bg-white p-2">
                                            <div class="overflow-hidden rounded-[18px] bg-[#E8E8E8]">
                                                <img :src="example.image_url" :alt="example.title" class="category-show-example-image h-[240px] w-full object-cover" loading="lazy" />
                                            </div>
                                        </div>

                                        <div v-if="isExampleSelected(example.id)" class="absolute right-4 top-4 rounded-full bg-[#20243B] p-2 text-white shadow-[0px_16px_30px_rgba(20,23,45,0.22)]"><Check class="h-4 w-4" /></div>

                                        <div class="mt-4 flex items-start justify-between gap-4">
                                            <div>
                                                <h2 class="category-show-example-title line-clamp-2 font-onest text-[24px] font-medium leading-tight text-[#20243B]">{{ example.title }}</h2>
                                                <p v-if="example.mood" class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">{{ example.mood }}</p>
                                            </div>
                                            <div class="category-show-example-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]"><ArrowRight class="h-4 w-4" /></div>
                                        </div>

                                        <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>

                                        <div class="mt-5 space-y-3">
                                            <p v-if="example.summary" class="category-show-example-description line-clamp-3 text-sm leading-6 text-[#5C6079]">{{ example.summary }}</p>
                                            <p v-else-if="example.location_hint || example.season_hint || example.clothing_hint" class="category-show-example-description line-clamp-3 text-sm leading-6 text-[#5C6079]">{{ [example.location_hint, example.season_hint, example.clothing_hint].filter((value) => value).join(' • ') }}</p>
                                            <p v-else class="text-sm leading-6 text-[#7A809E]">Подходящие теги появятся здесь после назначения фильтров.</p>
                                        </div>

                                        <div v-if="example.filter_option_labels.length > 0" class="mt-5 flex flex-wrap gap-2">
                                            <Badge v-for="label in example.filter_option_labels.slice(0, 4)" :key="`${example.id}-${label}`" variant="secondary" class="category-show-chip border border-[#D9DCF3] bg-white px-3 py-1.5 text-[#4252FF]">{{ label }}</Badge>
                                        </div>
                                    </div>
                                </article>
                            </div>

                            <div v-else class="mt-6 rounded-[28px] border border-dashed border-[#D9DCF3] bg-[#F7F8FF] p-5 text-sm leading-6 text-[#5C6079]">Нет фотографий, подходящих под выбранные фильтры.</div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Локации</p>
                                    <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">Рекомендуемые локации</h2>
                                </div>
                                <Link href="/locations" class="inline-flex items-center gap-2 text-sm text-[#5C6079] transition hover:text-[#20243B]"><span>Смотреть все локации</span><ArrowRight class="h-4 w-4" /></Link>
                            </div>

                            <div v-if="locations.length > 0" class="mt-6 grid gap-5 sm:grid-cols-2">
                                <article v-for="location in locations" :key="location.id" class="category-show-location-card group overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                    <div class="category-show-location-accent"></div>
                                    <div class="relative z-10">
                                        <div class="category-show-location-media rounded-[22px] border-b border-dashed border-black/8 bg-white p-2"><div class="overflow-hidden rounded-[18px] bg-[#E8E8E8]"><img :src="location.image_url" :alt="location.name" class="category-show-location-image h-[220px] w-full object-cover" loading="lazy" /></div></div>
                                        <div class="mt-4 flex items-start justify-between gap-4">
                                            <div class="flex items-start gap-3">
                                                <div class="category-show-location-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-white"><MapPinned class="h-5 w-5" /></div>
                                                <div>
                                                    <h3 class="category-show-location-title font-onest text-[24px] font-medium leading-none text-[#20243B]">{{ location.name }}</h3>
                                                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Рекомендованная локация</p>
                                                </div>
                                            </div>
                                            <div class="category-show-location-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]"><ArrowRight class="h-4 w-4" /></div>
                                        </div>
                                        <div v-if="location.filter_option_labels.length > 0" class="mt-5 flex flex-wrap gap-2"><Badge v-for="label in location.filter_option_labels.slice(0, 3)" :key="`${location.id}-${label}`" variant="secondary" class="category-show-chip border border-[#D9DCF3] bg-white px-3 py-1.5 text-[#4252FF]">{{ label }}</Badge></div>
                                    </div>
                                </article>
                            </div>

                            <div v-else class="mt-6 rounded-[28px] border border-dashed border-[#D9DCF3] bg-[#F7F8FF] p-5 text-sm leading-6 text-[#5C6079]">Нет локаций, подходящих под выбранные фильтры для этой категории.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>