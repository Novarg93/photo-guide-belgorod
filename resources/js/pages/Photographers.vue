<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Camera, CircleCheck, ExternalLink, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

interface PhotographerCard {
    id: number;
    name: string;
    slug: string;
    profile_url: string | null;
    image_url: string;
    description: string | null;
    url: string;
}

const props = defineProps<{
    photographers: PhotographerCard[];
    metaTitle: string;
    metaDescription: string;
}>()

const linkedProfilesCount = computed<number>(() => {
    return props.photographers.filter((photographer) => photographer.profile_url).length
})

const photographerHighlights = [
    {
        title: 'Прямые ссылки на профили',
        description: 'Переходите от короткого списка сразу к внешнему профилю, без ручного поиска.',
    },
    {
        title: 'Быстрое сравнение',
        description: 'Смотрите имена, описания и превью фотографий в одной аккуратной сетке.',
    },
    {
        title: 'Удобный сценарий выбора',
        description: 'Используйте каталог как первый шаг перед выбором локации или составлением брифа.',
    },
]

const photographerDescription = (photographer: PhotographerCard): string => {
    if (photographer.description) {
        return photographer.description
    }

    return 'Профиль фотографа доступен для просмотра в каталоге Белгорода.'
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
                                        Фотографы
                                    </span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">
                                        каталог
                                    </span>
                                </h1>

                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    Просматривайте доступных фотографов, сравнивайте профили и открывайте их публичные ссылки прямо с одной страницы.
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-[20px] bg-[#F7F8FF] px-4 py-4">
                                        <p class="font-onest text-sm font-medium text-[#A0A3B8]">Доступные фотографы</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none text-[#20243B]">{{ photographers.length }}</span>
                                            <span class="pb-1 text-sm text-[#5C6079]">профилей для просмотра</span>
                                        </div>
                                    </div>

                                    <div class="rounded-[20px] bg-[#20243B] px-4 py-4 text-white">
                                        <p class="font-onest text-sm font-medium text-white/60">Прямые внешние ссылки</p>
                                        <div class="mt-3 flex items-end gap-3">
                                            <span class="font-onest text-5xl font-medium leading-none">{{ linkedProfilesCount }}</span>
                                            <span class="pb-1 text-sm text-white/70">готово к открытию</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-[18px] border border-[#E2E5F6] bg-white px-4 py-3 text-sm leading-6 text-[#303651]">
                                    Используйте эту страницу как удобный короткий список перед переходом на внешние профили или дальнейшим планированием съемки.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[0.72fr_0.28fr]">
                    <div
                        v-if="photographers.length > 0"
                        class="grid gap-5 sm:grid-cols-2"
                    >
                        <article
                            v-for="photographer in photographers"
                            :key="photographer.id"
                            class="photographers-page-card group relative overflow-hidden rounded-[28px] bg-white p-4 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                        >
                            <Link
                                :href="photographer.url"
                                :aria-label="`Открыть страницу профиля ${photographer.name}`"
                                class="absolute inset-0 z-10 rounded-[28px]"
                            />

                            <div class="photographers-page-card-accent"></div>

                            <div class="relative z-0">
                                <div class="photographers-page-card-media rounded-[22px] border-b border-dashed border-black/8 bg-white p-2">
                                    <div class="overflow-hidden rounded-[18px] bg-[#E8E8E8]">
                                        <img
                                            :src="photographer.image_url"
                                            :alt="photographer.name"
                                            class="photographers-page-card-image h-[220px] w-full object-cover"
                                            loading="lazy"
                                        >
                                    </div>
                                </div>

                                <div class="mt-4 flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="photographers-page-card-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-white">
                                            <Camera class="h-5 w-5" />
                                        </div>

                                        <div>
                                            <h2 class="photographers-page-card-title font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                                {{ photographer.name }}
                                            </h2>

                                            <p class="mt-2 text-xs font-semibold uppercase tracking-[0.22em] text-[#8A8FAF]">
                                                Профиль фотографа
                                            </p>
                                        </div>
                                    </div>

                                    <div class="photographers-page-card-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]">
                                        <ArrowRight class="h-4 w-4" />
                                    </div>
                                </div>

                                <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>

                                <p class="photographers-page-card-description mt-5 text-sm leading-6 text-[#5C6079]">
                                    {{ photographerDescription(photographer) }}
                                </p>

                                <div class="mt-5 flex flex-wrap items-center gap-3">
                                    <div class="inline-flex items-center gap-2 rounded-full border border-[#E2E5F6] bg-[#F7F8FF] px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#7A809E]">
                                        <CircleCheck class="h-3.5 w-3.5" />
                                        <span>Профиль в каталоге</span>
                                    </div>

                                    <a
                                        v-if="photographer.profile_url"
                                        :href="photographer.profile_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        @click.stop
                                        class="photographers-page-card-link relative z-20 inline-flex items-center gap-2 rounded-full border border-[#D9DCF3] bg-white px-4 py-2 text-sm font-medium text-[#4252FF]"
                                    >
                                        <span>Открыть профиль</span>
                                        <ExternalLink class="h-4 w-4" />
                                    </a>
                                    <div
                                        v-else
                                        class="inline-flex items-center gap-2 rounded-full border border-black/8 bg-[#F7F8FF] px-4 py-2 text-sm font-medium text-[#8A8FAF]"
                                    >
                                        <span>Ссылка на профиль недоступна</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div
                        v-else
                        class="rounded-[28px] bg-white p-6 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                    >
                        <p class="font-onest text-[24px] font-medium text-[#20243B]">Каталог фотографов пуст.</p>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-[#5C6079]">
                            Добавьте фотографов в админ-панели, и они появятся здесь в публичном каталоге.
                        </p>
                    </div>

                    <aside class="space-y-4">
                        <article
                            v-for="highlight in photographerHighlights"
                            :key="highlight.title"
                            class="photographers-page-note rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                        >
                            <div class="flex h-11 w-11 items-center justify-center rounded-[16px] bg-primary text-white">
                                <Sparkles class="h-5 w-5" />
                            </div>

                            <h2 class="mt-5 font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                {{ highlight.title }}
                            </h2>

                            <p class="mt-4 text-sm leading-6 text-[#5C6079]">
                                {{ highlight.description }}
                            </p>
                        </article>
                    </aside>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>