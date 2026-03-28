<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ArrowRight } from 'lucide-vue-next'
import { useRevealOnScroll } from '@/composables/useRevealOnScroll'

const { sectionRef, isVisible } = useRevealOnScroll({
    threshold: 0.2,
    rootMargin: '0px 0px -10% 0px',
    once: true,
})
</script>

<template>
    <section ref="sectionRef" class="py-20 bg-[#e8e8e8] w-full">
        <div class="max-w-7xl px-5 mx-auto">
            <div class="mb-8 flex flex-col gap-5 md:mb-10 md:flex-row md:items-center md:justify-between">
                <h2 :class="[
                    'photographers-title-enter text-[#20243B] text-center',
                    isVisible && 'reveal-active',
                ]">
                    <span class="text-[#CBCBCB] font-onest text-2xl! font-medium relative -top-1 ">(</span><span
                        class="font-onest text-[33px] leading-none tracking-[-0.01em] font-medium">
                        Найдите своего
                    </span>
                    <span
                        class="font-playfair ml-2 text-[41px] leading-none tracking-[-0.01em] font-semibold italic text-[#4252FF]">
                        фотографа
                    </span> <span class="text-[#CBCBCB] font-onest text-2xl! font-medium relative -top-1 ">)</span>
                </h2>

                <Link href="/catalog" :class="[
                    'photographers-cta-enter button-primary mx-auto md:mx-0 inline-flex items-center gap-2 self-start px-6 py-4 md:self-auto',
                    isVisible && 'reveal-active',
                ]">
                    <span>Другие профили</span>
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div :class="[
                    'photographers-card-enter photographers-card p-3 rounded-2xl bg-white flex flex-col gap-3 relative',
                    isVisible && 'reveal-active',
                ]" :style="{ '--reveal-delay': '0.18s' }">
                    <div
                        class="photographers-badge absolute -rotate-3 z-10 top-30 right-2 bg-white py-1 px-3 text-primary border border-primary rounded-3xl w-fit ">
                        <span class="font-onest text-[17px] font-medium">5</span> лет опыта</div>
                    <img class="photographers-image h-36 w-full object-cover" src="/images/photo.png" alt="">
                    <h3 class="font-onest font-medium text-xl">Ольга Шевцова</h3>
                    <div
                        class="h-[2px] w-full bg-[repeating-linear-gradient(to_right,#D1D5DB_0_16px,transparent_16px_28px)]">
                    </div>
                    <p class="text-[15px]">Принимаю заказы на фотосъемку различных жанров и буду рада запечатлеть для
                        вас важные события и моменты вашей жизни!</p>
                </div>

                <div :class="[
                    'photographers-card-enter photographers-card p-3 rounded-2xl bg-white flex flex-col gap-3 relative',
                    isVisible && 'reveal-active',
                ]" :style="{ '--reveal-delay': '0.18s' }">
                    <div
                        class="photographers-badge absolute -rotate-3 z-10 top-30 right-2 bg-white py-1 px-3 text-primary border border-primary rounded-3xl w-fit ">
                        <span class="font-onest text-[17px] font-medium">5</span> лет опыта</div>
                    <img class="photographers-image h-36 w-full object-cover" src="/images/photo.png" alt="">
                    <h3 class="font-onest font-medium text-xl">Ольга Шевцова</h3>
                    <div
                        class="h-[2px] w-full bg-[repeating-linear-gradient(to_right,#D1D5DB_0_16px,transparent_16px_28px)]">
                    </div>
                    <p class="text-[15px]">Принимаю заказы на фотосъемку различных жанров и буду рада запечатлеть для
                        вас важные события и моменты вашей жизни!</p>
                </div>

                <div :class="[
                    'photographers-card-enter photographers-card p-3 rounded-2xl bg-white flex flex-col gap-3 relative',
                    isVisible && 'reveal-active',
                ]" :style="{ '--reveal-delay': '0.18s' }">
                    <div
                        class="photographers-badge absolute -rotate-3 z-10 top-30 right-2 bg-white py-1 px-3 text-primary border border-primary rounded-3xl w-fit ">
                        <span class="font-onest text-[17px] font-medium">5</span> лет опыта</div>
                    <img class="photographers-image h-36 w-full object-cover" src="/images/photo.png" alt="">
                    <h3 class="font-onest font-medium text-xl">Ольга Шевцова</h3>
                    <div
                        class="h-[2px] w-full bg-[repeating-linear-gradient(to_right,#D1D5DB_0_16px,transparent_16px_28px)]">
                    </div>
                    <p class="text-[15px]">Принимаю заказы на фотосъемку различных жанров и буду рада запечатлеть для
                        вас важные события и моменты вашей жизни!</p>
                </div>

                <div :class="[
                    'photographers-card-enter photographers-card p-3 rounded-2xl bg-white flex flex-col gap-3 relative',
                    isVisible && 'reveal-active',
                ]" :style="{ '--reveal-delay': '0.18s' }">
                    <div
                        class="photographers-badge absolute -rotate-3 z-10 top-30 right-2 bg-white py-1 px-3 text-primary border border-primary rounded-3xl w-fit ">
                        <span class="font-onest text-[17px] font-medium">5</span> лет опыта</div>
                    <img class="photographers-image h-36 w-full object-cover" src="/images/photo.png" alt="">
                    <h3 class="font-onest font-medium text-xl">Ольга Шевцова</h3>
                    <div
                        class="h-[2px] w-full bg-[repeating-linear-gradient(to_right,#D1D5DB_0_16px,transparent_16px_28px)]">
                    </div>
                    <p class="text-[15px]">Принимаю заказы на фотосъемку различных жанров и буду рада запечатлеть для
                        вас важные события и моменты вашей жизни!</p>
                </div>
            </div>
        </div>
    </section>
</template>
