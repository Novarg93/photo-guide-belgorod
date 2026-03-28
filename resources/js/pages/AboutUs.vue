<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

const props = defineProps<{
    metaTitle: string;
    metaDescription: string;
}>()

const pillars = [
    {
        title: 'Зачем существует этот проект',
        description:
            'Мы создали этот проект, чтобы собрать всю информацию о фотосессиях в одном месте: категории, референсы, локации, фотографов и советы по подготовке.',
    },
    {
        title: 'Какую проблему мы решаем',
        description:
            'Большинство людей тратят слишком много времени на поиск в чатах и соцсетях. Мы убираем этот хаос с помощью структурированных фильтров, готовых примеров и понятного процесса создания брифа.',
    },
    {
        title: 'Наши планы',
        description:
            'Дальше — больше контента, улучшение фильтров, развитие локальных гайдов и создание удобных инструментов для быстрого общения между клиентами и фотографами.',
    },
] as const

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
                            href="/"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>На главную</span>
                        </Link>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
                            <div>
                                <h1 class="text-[#20243B]">
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">
                                        О
                                    </span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">
                                        нас
                                    </span>
                                </h1>

                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    Photo Guide Belgorod — это локальный проект, который помогает быстрее и проще планировать фотосессии.
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <p class="font-onest text-sm font-medium text-[#A0A3B8]">Фокус проекта</p>
                                <div class="mt-3 space-y-3">
                                    <div class="rounded-[18px] bg-[#F7F8FF] px-4 py-3 text-sm leading-6 text-[#303651]">
                                        Локальные категории, референсы, локации, фотографы и практические рекомендации.
                                    </div>
                                    <div class="inline-flex items-center gap-2 rounded-full border border-[#D9DCF3] bg-white px-3 py-1.5 text-xs font-medium text-[#4252FF]">
                                        <ArrowRight class="h-3.5 w-3.5" />
                                        Структурированное планирование вместо хаотичных переписок
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <article
                        v-for="(pillar, index) in pillars"
                        :key="pillar.title"
                        class="about-card relative overflow-hidden rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                    >
                        <div class="about-card-accent"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between gap-4">
                                <div class="about-card-index flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-sm font-semibold text-white">
                                    {{ String(index + 1).padStart(2, '0') }}
                                </div>

                                <div class="about-card-arrow flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#D9DCF3] text-[#4252FF]">
                                    <ArrowRight class="h-4 w-4" />
                                </div>
                            </div>

                            <h2 class="about-card-title mt-5 font-onest text-[24px] font-medium leading-none text-[#20243B]">
                                {{ pillar.title }}
                            </h2>

                            <div class="mt-5 h-[1px] w-full bg-[repeating-linear-gradient(to_right,rgba(32,36,59,0.18)_0_16px,transparent_16px_28px)]"></div>

                            <p class="mt-5 text-sm leading-6 text-[#5C6079]">
                                {{ pillar.description }}
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>