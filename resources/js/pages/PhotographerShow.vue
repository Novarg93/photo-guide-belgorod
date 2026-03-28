<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Camera, ExternalLink, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

interface PhotographerDetail {
    id: number;
    name: string;
    slug: string;
    profile_url: string | null;
    image_url: string;
    description: string | null;
    url: string;
}

const props = defineProps<{
    photographer: PhotographerDetail;
    metaTitle: string;
    metaDescription: string;
}>()

const photographerDescription = computed<string>(() => {
    if (props.photographer.description) {
        return props.photographer.description
    }

    return `Фотограф ${props.photographer.name} доступен в каталоге Белгорода.`
})

const detailInsights = computed(() => {
    return [
        {
            title: 'Обзор профиля',
            description: 'Отдельная страница для просмотра фотографа перед переходом во внешний профиль или возвратом в каталог.',
        },
        {
            title: 'Точка принятия решения',
            description: 'Полезно, когда нужен один аккуратный экран с ключевой информацией вместо сравнения профилей в шумном списке.',
        },
        {
            title: 'Следующий шаг',
            description: 'Откройте внешний профиль, если он доступен, или продолжайте просмотр каталога фотографов в поиске других вариантов.',
        },
    ]
})

useHead(() => ({
    title: props.metaTitle,
    meta: [{ key: 'description', name: 'description', content: props.metaDescription }],
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
                            href="/photographers"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>Назад к фотографам</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.08fr_0.92fr] lg:items-end">
                            <div>
                                <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">
                                    <Camera class="h-3.5 w-3.5" />
                                    <span>Профиль фотографа</span>
                                </p>

                                <h1 class="mt-4 max-w-4xl text-balance font-onest text-[38px] font-medium leading-[0.94] tracking-[-0.03em] text-[#20243B] md:text-[58px]">
                                    {{ photographer.name }}
                                </h1>

                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    {{ photographerDescription }}
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-[20px] bg-[#F7F8FF] px-4 py-4">
                                        <p class="font-onest text-sm font-medium text-[#A0A3B8]">Статус профиля</p>
                                        <div class="mt-3 text-lg font-medium leading-tight text-[#20243B]">
                                            {{ photographer.profile_url ? 'Внешний профиль доступен' : 'Только запись в каталоге' }}
                                        </div>
                                    </div>

                                    <div class="rounded-[20px] bg-[#20243B] px-4 py-4 text-white">
                                        <p class="font-onest text-sm font-medium text-white/60">Slug фотографа</p>
                                        <p class="mt-3 break-all font-onest text-xl font-medium leading-tight">
                                            {{ photographer.slug }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-[18px] border border-[#E2E5F6] bg-white px-4 py-3 text-sm leading-6 text-[#303651]">
                                    Сначала изучите фотографа здесь, а затем переходите во внешний профиль, если хотите продолжить просмотр за пределами каталога.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[0.72fr_0.28fr]">
                    <article class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6">
                        <div class="photographer-show-image-shell rounded-[26px] bg-[#F7F8FF] p-3">
                            <div class="overflow-hidden rounded-[22px] border border-black/6 bg-[#E8E8E8]">
                                <img
                                    :src="photographer.image_url"
                                    :alt="photographer.name"
                                    class="photographer-show-image h-[260px] w-full object-cover md:h-[420px]"
                                >
                            </div>
                        </div>

                        <div class="mt-6 rounded-[26px] border border-[#E2E5F6] bg-[#FCFCFF] p-5 md:p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[18px] bg-primary text-white">
                                    <Camera class="h-5 w-5" />
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">О фотографе</p>
                                    <p class="mt-3 text-sm leading-7 text-[#4F556F] md:text-base">
                                        {{ photographerDescription }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Действие</p>
                                    <h2 class="mt-2 font-onest text-[28px] font-medium leading-none text-[#20243B]">
                                        Перейти к профилю
                                    </h2>
                                </div>
                            </div>

                            <div class="mt-6 rounded-[26px] bg-[#F7F8FF] p-4 md:p-5">
                                <a
                                    v-if="photographer.profile_url"
                                    :href="photographer.profile_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="photographer-show-cta group flex items-center justify-between gap-4 rounded-[22px] bg-white px-5 py-4 text-[#20243B] shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                                >
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8A8FAF]">Внешний профиль</p>
                                        <p class="mt-2 text-sm leading-6 text-[#5C6079]">
                                            Откройте профиль фотографа в новой вкладке и продолжайте с прямым контактом или просмотром портфолио.
                                        </p>
                                    </div>

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]">
                                        <ExternalLink class="photographer-show-cta-arrow h-4 w-4" />
                                    </div>
                                </a>

                                <div
                                    v-else
                                    class="rounded-[22px] border border-dashed border-[#D9DCF3] bg-white px-5 py-4 text-sm leading-6 text-[#5C6079]"
                                >
                                    Ссылка на внешний профиль для этого фотографа пока недоступна.
                                </div>
                            </div>
                        </div>
                    </article>

                    <aside class="space-y-4">
                        <article
                            v-for="insight in detailInsights"
                            :key="insight.title"
                            class="photographer-show-note rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                        >
                            <div class="flex h-11 w-11 items-center justify-center rounded-[16px] bg-primary text-white">
                                <Sparkles class="h-5 w-5" />
                            </div>

                            <h2 class="mt-5 font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                {{ insight.title }}
                            </h2>

                            <p class="mt-4 text-sm leading-6 text-[#5C6079]">
                                {{ insight.description }}
                            </p>
                        </article>

                        <Link
                            href="/photographers"
                            class="photographer-show-back group block rounded-[28px] bg-[#20243B] p-5 text-white shadow-[0px_18px_40px_rgba(20,23,45,0.14)]"
                        >
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Продолжить просмотр</p>
                            <p class="mt-4 font-onest text-[24px] font-medium leading-none">Назад ко всем фотографам</p>
                            <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-white/14 bg-white/8 px-4 py-2 text-sm font-medium text-white">
                                <span>Открыть список</span>
                                <ArrowRight class="photographer-show-back-arrow h-4 w-4" />
                            </div>
                        </Link>
                    </aside>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>