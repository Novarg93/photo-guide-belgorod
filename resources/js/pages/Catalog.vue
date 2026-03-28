<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'
import { home } from '@/routes'

interface CategoryCard {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    filter_groups: {
        key: string;
        label: string;
        options: { key: string; label: string }[];
    }[];
    url: string;
}

const props = defineProps<{
    categories: CategoryCard[];
    metaTitle: string;
    metaDescription: string;
}>()

const categorySummary = (category: CategoryCard): string => {
    if (category.description) {
        return category.description
    }

    return 'Описание категории появится позже.'
}

const groupPreview = (category: CategoryCard): string[] => {
    return category.filter_groups
        .flatMap((group) => group.options.map((option) => option.label))
        .slice(0, 4)
}

useHead(() => ({
    title: props.metaTitle,
    meta: [
        { key: 'description', name: 'description', content: props.metaDescription },
    ],
}))
</script>

<template>
    <AppHeaderLayout>
        <section class="bg-[#f5f5f5] pb-64 pt-32 md:pb-80">
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
                            :href="home()"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>На главную</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
                            <div>
                                <h1 class="text-[#20243B]">
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">
                                        Каталог
                                    </span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">
                                        фотосессий
                                    </span>
                                </h1>

                                <p class="mt-5 max-w-2xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    Выберите категорию, посмотрите доступные фильтры и перейдите к подходящему сценарию фотосессии в том же визуальном стиле, что и на главной странице.
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <p class="font-onest text-sm font-medium text-[#A0A3B8]">Доступные категории</p>
                                <div class="mt-3 flex items-end gap-3">
                                    <span class="font-onest text-5xl font-medium leading-none text-[#20243B]">{{ categories.length }}</span>
                                    <span class="pb-1 text-sm text-[#5C6079]">вариантов для старта</span>
                                </div>

                                <div class="mt-5 flex flex-wrap gap-2">
                                    <span
                                        v-for="category in categories.slice(0, 4)"
                                        :key="category.id"
                                        class="rounded-full border border-primary/10 bg-primary/6 px-3 py-1.5 text-xs font-medium text-primary"
                                    >
                                        {{ category.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <Link
                        v-for="(category, index) in categories"
                        :key="category.id"
                        :href="category.url"
                        class="catalog-card group relative overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                    >
                        <div class="catalog-card-accent"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="catalog-card-index flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-sm font-semibold text-white">
                                        {{ String(index + 1).padStart(2, '0') }}
                                    </div>

                                    <div>
                                        <h2 class="catalog-card-title font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                            {{ category.name }}
                                        </h2>
                                        <p class="mt-3 max-w-[44ch] text-sm leading-6 text-[#5C6079]">
                                            {{ categorySummary(category) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="catalog-card-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]">
                                    <ArrowRight class="h-4 w-4" />
                                </div>
                            </div>

                            <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>

                            <div
                                v-if="category.filter_groups.length > 0"
                                class="mt-5 space-y-4"
                            >
                                <div
                                    v-for="group in category.filter_groups"
                                    :key="group.key"
                                    class="rounded-[20px] bg-[#F7F8FF] px-4 py-3"
                                >
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#8A8FAF]">
                                        {{ group.label }}
                                    </p>
                                    <p class="mt-2 text-sm leading-6 text-[#303651]">
                                        {{ group.options.map((option) => option.label).join(', ') }}
                                    </p>
                                </div>
                            </div>

                            <div v-else class="mt-5 rounded-[20px] bg-[#F7F8FF] px-4 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#8A8FAF]">
                                    Фильтры
                                </p>
                                <p class="mt-2 text-sm leading-6 text-[#303651]">
                                    Наборы фильтров появятся здесь позже.
                                </p>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <span
                                    v-for="label in groupPreview(category)"
                                    :key="`${category.id}-${label}`"
                                    class="catalog-chip rounded-full border border-[#D9DCF3] bg-white px-3 py-1.5 text-xs font-medium text-[#4252FF]"
                                >
                                    {{ label }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>