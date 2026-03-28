<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { MapPin, Heart, Camera } from 'lucide-vue-next'
import { useRevealOnScroll } from '@/composables/useRevealOnScroll'

defineProps<{
    locations: Array<{
        id: number;
        name: string;
        description: string | null;
        image_url: string;
        url: string;
    }>;
}>()

const { sectionRef, isVisible } = useRevealOnScroll({
    threshold: 0.2,
    rootMargin: '0px 0px -10% 0px',
    once: true,
})

const locationDescription = (description: string | null) => {
    return description || 'Подходящее место для фотосессии в Белгороде.'
}
</script>

<template>
    <section ref="sectionRef" class="bg-card">
        <div class="mx-auto w-full max-w-7xl px-5 py-10 md:py-14">
            <div class="relative  rounded-[32px] bg-card  py-10  md:py-14">
                <div class="grid-overlay-hero">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>

                <div class="relative z-10">
                    <div class="mb-14 grid gap-14 md:gap-8 lg:gap-8 md:grid-cols-3 md:items-start">
                        <div
                            :class="[
                                'locations-step-card locations-step-card-1 relative overflow-hidden rounded-xl bg-white py-3 rotate-6',
                                isVisible && 'reveal-active',
                            ]"
                        >
                            <div class="flex items-center gap-4">
                                <div class="rounded-r-full bg-primary p-2 px-4 text-white">
                                    1
                                </div>
                                <span class="font-onest text-xl font-medium">Найдите стиль и референсы</span>
                            </div>

                            <div class="relative mt-4">
                                <div
                                    :class="[
                                        'locations-step-inner-1 flex min-w-full w-fit items-start justify-center gap-4 px-6',
                                        isVisible && 'reveal-active',
                                    ]"
                                >
                                    <div class="shrink-0">
                                        <div class="rounded-xl border-b border-dashed shadow">
                                            <div class="flex items-center gap-2 rounded-xl py-2">
                                                <div class="rounded-r-full bg-primary p-1 px-2 text-white">
                                                    <Heart class="size-4" />
                                                </div>
                                                <span class="font-onest text-sm font-medium">Love Story</span>
                                            </div>
                                            <div class="px-2 pb-1 text-[9px]">
                                                Романтические фотографии для пары.
                                                <br>
                                                Красивые моменты и эмоции вдвоем.
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-2 rounded-xl border-t border-dashed p-2 shadow">
                                            <div class="flex flex-col gap-2 text-[9px]">
                                                <div class="w-fit rounded-xl bg-[#e6e6e7] px-2 py-0.5">
                                                    Настроение
                                                </div>
                                                <span>Нежно, романтично, атмосферно</span>
                                            </div>

                                            <div class="flex flex-col gap-2 text-[9.5px]">
                                                <div class="w-fit rounded-xl bg-[#e6e6e7] px-2 py-0.5">
                                                    Одежда
                                                </div>
                                                <span>Повседневный, элегантный, легкий</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <div class="rounded-xl border-b border-dashed shadow">
                                            <div class="flex items-center gap-2 rounded-xl py-2">
                                                <div class="rounded-r-full bg-primary p-1 px-2 text-white">
                                                    <Heart class="size-4" />
                                                </div>
                                                <span class="font-onest text-sm font-medium">Семья</span>
                                            </div>
                                            <div class="px-2 pb-1 text-[9px]">
                                                Теплые фотографии с близкими -
                                                <br>
                                                прогулка, объятия и живые моменты.
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-2 rounded-xl border-t border-dashed p-2 shadow">
                                            <div class="flex flex-col gap-2 text-[9px]">
                                                <div class="w-fit rounded-xl bg-[#e6e6e7] px-2 py-0.5">
                                                    Настроение
                                                </div>
                                                <span>Спокойно, тепло, уютно</span>
                                            </div>

                                            <div class="flex flex-col gap-2 text-[9px]">
                                                <div class="w-fit rounded-xl bg-[#e6e6e7] px-2 py-0.5">
                                                    Одежда
                                                </div>
                                                <span>Повседневный, классический</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <div class="rounded-xl border-b border-dashed shadow">
                                            <div class="flex items-center gap-2 rounded-xl py-2">
                                                <div class="rounded-r-full bg-primary p-1 px-2 text-white">
                                                    <Heart class="size-4" />
                                                </div>
                                                <span class="font-onest text-sm font-medium">Детская</span>
                                            </div>
                                            <div class="px-2 pb-1 text-[9px]">
                                                Живые и радостные фотографии.
                                                <br>
                                                Игра, движение и настроение
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-2 rounded-xl border-t border-dashed p-2 shadow">
                                            <div class="flex flex-col gap-2 text-[9px]">
                                                <div class="w-fit rounded-xl bg-[#e6e6e7] px-2 py-0.5">
                                                    Настроение
                                                </div>
                                                <span>Весело, игриво, живо</span>
                                            </div>

                                            <div class="flex flex-col gap-2 text-[9px]">
                                                <div class="w-fit rounded-xl bg-[#e6e6e7] px-2 py-0.5">
                                                    Одежда
                                                </div>
                                                <span>Повседневный, яркий, уютный</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pointer-events-none absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white to-transparent"></div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white to-transparent"></div>
                            </div>
                        </div>

                        <div
                            :class="[
                                'locations-step-card locations-step-card-2 relative h-60 shrink-0 overflow-hidden rounded-xl bg-white py-3',
                                isVisible && 'reveal-active',
                            ]"
                        >
                            <div class="flex items-center gap-4">
                                <div class="rounded-r-full bg-primary p-2 px-4 text-white">
                                    2
                                </div>
                                <span class="font-onest text-xl font-medium">Выберите локацию съемки</span>
                            </div>

                            <div class="relative mt-4">
                                <div
                                    :class="[
                                        'locations-step-inner-2 mx-auto h-40 w-3/4 rounded-2xl border border-primary p-2 px-0',
                                        isVisible && 'reveal-active',
                                    ]"
                                >
                                    <img
                                        class="locations-step-photo w-full object-cover"
                                        src="/images/photo.png"
                                        alt="Пример локации"
                                    >
                                </div>

                                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
                            </div>
                        </div>

                        <div
                            :class="[
                                'locations-step-card locations-step-card-3 relative overflow-hidden rounded-xl bg-white py-3 -rotate-6',
                                isVisible && 'reveal-active',
                            ]"
                        >
                            <div class="flex items-center gap-4">
                                <div class="rounded-r-full bg-primary p-2 px-4 text-white">
                                    3
                                </div>
                                <span class="font-onest text-xl font-medium">Отправьте фотографу готовый бриф</span>
                            </div>

                            <div
                                :class="[
                                    'locations-step-inner-3 relative mt-5 px-6 pb-6',
                                    isVisible && 'reveal-active',
                                ]"
                            >
                                <div class="flex justify-end">
                                    <div class="flex items-center gap-2 text-[11px] text-[#a0a0a0]">
                                        <span>00:00 AM</span>
                                        <div class="flex h-5 w-5 items-center justify-center rounded-full border border-primary/20 text-primary">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M5 19a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-1 flex justify-end">
                                    <div class="inline-flex items-center gap-2 rounded-full border border-primary bg-white px-4 py-2 text-primary shadow-sm">
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none">
                                            <path d="M8 3.75h6l4.25 4.25V19.5A1.5 1.5 0 0 1 16.75 21h-8.5A1.5 1.5 0 0 1 6.75 19.5v-14A1.5 1.5 0 0 1 8.25 4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                            <path d="M14 3.75V8h4.25" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                        </svg>
                                        <span class="font-onest text-base leading-none">Brief.docx</span>
                                        <span class="text-lg leading-none">👋</span>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-2 text-[11px] text-[#a0a0a0]">
                                    <div class="flex h-5 w-5 items-center justify-center rounded-full border border-primary/10 text-primary/70">
                                        <Camera class="size-4" />
                                    </div>
                                    <span>00:00 AM</span>
                                </div>

                                <div class="mt-1">
                                    <div class="inline-flex items-center gap-2 rounded-full border border-primary bg-white px-4 py-2 text-primary shadow-sm">
                                        <span class="text-base leading-none">🔥</span>
                                        <span class="font-onest text-base leading-none">Круто, взяла в работу!</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 text-center">
                        <h2
                            :class="[
                                'locations-title-enter text-[#20243B]',
                                isVisible && 'reveal-active',
                            ]"
                        >
                            <span class="font-onest text-[33px] font-medium leading-none tracking-[-0.01em]">
                                <span class="relative -top-1 text-2xl! font-medium text-[#CBCBCB]">(</span>
                                Подходящие
                            </span>
                            <span class="ml-2 font-playfair text-[41px] font-semibold italic leading-none tracking-[-0.01em] text-[#4252FF]">
                                локации
                            </span>
                            <span class="relative -top-1 font-onest text-2xl! font-medium text-[#CBCBCB]">)</span>
                        </h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <Link
                            v-for="(location, index) in locations"
                            :key="location.id"
                            :href="location.url"
                            :class="[
                                'locations-card-enter group block',
                                isVisible && 'reveal-active',
                            ]"
                            :style="{ '--reveal-delay': `${0.18 + index * 0.08}s` }"
                        >
                            <div class="locations-card-media rounded-xl border-b border-dashed bg-white p-2 pb-4 shadow-xl">
                                <img
                                    :src="location.image_url"
                                    :alt="location.name"
                                    class="locations-card-image h-[140px] w-full rounded-[16px] object-cover"
                                >
                            </div>

                            <div class="locations-card-content rounded-xl bg-white shadow-xl">
                                <div class="py-3">
                                    <div class="mb-3 flex items-center gap-3">
                                        <div class="locations-card-pin flex h-8 w-8 items-center justify-center rounded-l-[10px] rounded-r-[999px] bg-primary text-white">
                                            <MapPin class="h-4 w-4" />
                                        </div>

                                        <h3 class="locations-card-name font-onest text-xlv font-medium leading-none bg-[linear-gradient(107.86deg,#14172D_62.24%,rgba(20,23,45,0.36)_100.82%)] bg-clip-text text-transparent">
                                            {{ location.name }}
                                        </h3>
                                    </div>

                                    <p class="locations-card-description line-clamp-2 px-4 text-sm leading-[1.35] text-[#14172D]">
                                        {{ locationDescription(location.description) }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>