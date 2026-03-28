<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, MapPinned, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

interface LocationCard {
    id: number;
    name: string;
    slug: string;
    category: string | null;
    image_url: string;
    description: string | null;
    url: string;
}

const props = defineProps<{
    locations: LocationCard[];
    metaTitle: string;
    metaDescription: string;
}>()

const locationDescription = (location: LocationCard): string => {
    if (location.description) {
        return location.description
    }

    return 'Локация в Белгороде, подходящая для планирования фотосессии.'
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
                            href="/catalog"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>Назад в каталог</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
                            <div>
                                <h1 class="text-[#20243B]">
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">
                                        Локации
                                    </span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">
                                        каталог
                                    </span>
                                </h1>

                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    Все доступные локации для фотосессий в Белгороде собраны в одном месте для быстрого просмотра и удобного сравнения.
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <p class="font-onest text-sm font-medium text-[#A0A3B8]">Доступные локации</p>
                                <div class="mt-3 flex items-end gap-3">
                                    <span class="font-onest text-5xl font-medium leading-none text-[#20243B]">{{ locations.length }}</span>
                                    <span class="pb-1 text-sm text-[#5C6079]">мест для просмотра</span>
                                </div>

                                <div class="mt-5 rounded-[18px] bg-[#F7F8FF] px-4 py-3 text-sm leading-6 text-[#303651]">
                                    Сравните атмосферу, соответствие категории и референсы перед переходом на страницу конкретной локации.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="locations.length > 0"
                    class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <Link
                        v-for="(location, index) in locations"
                        :key="location.id"
                        :href="location.url"
                        class="locations-page-card group block overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                    >
                        <div class="locations-page-card-accent"></div>

                        <div class="relative z-10">
                            <div class="locations-page-card-media rounded-[22px] border-b border-dashed border-black/8 bg-white p-2">
                                <div class="overflow-hidden rounded-[18px] bg-[#E8E8E8]">
                                    <img
                                        :src="location.image_url"
                                        :alt="location.name"
                                        class="locations-page-card-image h-[220px] w-full object-cover"
                                        loading="lazy"
                                    >
                                </div>
                            </div>

                            <div class="mt-4 flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="locations-page-card-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-white">
                                        <MapPinned class="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 class="locations-page-card-title font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                            {{ location.name }}
                                        </h2>

                                        <p
                                            v-if="location.category"
                                            class="mt-2 text-xs font-semibold uppercase tracking-[0.22em] text-[#8A8FAF]"
                                        >
                                            {{ location.category }}
                                        </p>
                                    </div>
                                </div>

                                <div class="locations-page-card-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]">
                                    <ArrowRight class="h-4 w-4" />
                                </div>
                            </div>

                            <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>

                            <p class="locations-page-card-description mt-5 text-sm leading-6 text-[#5C6079]">
                                {{ locationDescription(location) }}
                            </p>
                        </div>
                    </Link>
                </div>

                <div
                    v-else
                    class="mt-8 rounded-[28px] bg-white p-6 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                >
                    <p class="font-onest text-[24px] font-medium text-[#20243B]">Каталог локаций пуст.</p>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-[#5C6079]">
                        Добавьте локации в админ-панели, и они появятся здесь.
                    </p>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>