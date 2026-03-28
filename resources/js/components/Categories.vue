<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import { useRevealOnScroll } from '@/composables/useRevealOnScroll'
import { ArrowRight } from 'lucide-vue-next';

defineProps<{
    categories: Array<{
        id: number;
        name: string;
        title: string | null;
        description: string | null;
        url: string;
        image: string | null;
    }>;
}>()

const { sectionRef, isVisible } = useRevealOnScroll({
    threshold: 0.2,
    rootMargin: '0px 0px -10% 0px',
    once: true,
})

const fallbackImages = [
    '/images/category-1.jpg',
    '/images/category-2.jpg',
    '/images/category-3.jpg',
    '/images/category-4.jpg',
    '/images/category-5.jpg',
    '/images/category-6.jpg',
    '/images/category-7.jpg',
    '/images/category-8.jpg',
]

const categoryLabel = (category: { name: string; title: string | null }) => {
    return category.title || category.name
}
</script>

<template>
    <section ref="sectionRef" class="mx-auto w-full max-w-7xl px-5 py-8 md:py-20">
        <div class="mb-8 flex flex-col gap-5 md:mb-10 md:flex-row md:items-center md:justify-between">
            <h2
                :class="[
                    'formats-fade-up-1 text-[#20243B]',
                    isVisible && 'reveal-active',
                ]"
            >
                <span class="relative -top-1 font-onest text-2xl! font-medium text-[#CBCBCB]">(</span>
                <span class="font-onest text-[33px] leading-none tracking-[-0.01em] font-medium">
                    Популярные форматы
                </span>
                <span class="ml-2 font-playfair text-[41px] leading-none tracking-[-0.01em] font-semibold italic text-[#4252FF]">
                    съемок
                </span>
                <span class="relative -top-1 font-onest text-2xl! font-medium text-[#CBCBCB]">)</span>
            </h2>

            <Link
                href="/catalog"
                :class="[
                    'formats-fade-up-2 group button-primary inline-flex items-center gap-2 self-start px-6 py-4 md:self-auto',
                    isVisible && 'reveal-active',
                ]"
            >
                <span>Все категории</span>
                <ArrowRight class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5" />
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Link
                v-for="(category, index) in categories"
                :key="category.id"
                :href="category.url"
                :class="[
                    'group formats-card formats-card-enter relative block aspect-[1.35/1] overflow-hidden rounded-2xl bg-[#EAE6E1]',
                    isVisible && 'reveal-active',
                ]"
                :style="{ '--reveal-delay': `${0.18 + index * 0.1}s` }"
            >
                <img
                    :src="category.image || fallbackImages[index % fallbackImages.length]"
                    :alt="categoryLabel(category)"
                    class="formats-card-image h-full w-full object-cover"
                />
                <div class="formats-card-overlay absolute inset-0 bg-gradient-to-t from-black/15 via-black/[0.03] to-transparent"></div>
                <div class="formats-card-shine"></div>

                <div class="absolute bottom-3 left-0 z-10">
                    <div class="formats-card-badge rounded-r-3xl border border-l-2 border-[#4252FF] bg-white px-3 py-2">
                        <span class="text-[17px] leading-none font-normal text-[#20243B]">
                            {{ categoryLabel(category) }}
                        </span>
                    </div>
                </div>
            </Link>
        </div>
    </section>
</template>
