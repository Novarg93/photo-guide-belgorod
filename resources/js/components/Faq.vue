<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion'
import { useRevealOnScroll } from '@/composables/useRevealOnScroll'

type Faq = {
    id: number
    question: string
    answer: string
}

defineProps<{
    faqs: Faq[]
}>()

const { sectionRef, isVisible } = useRevealOnScroll({
    threshold: 0.2,
    rootMargin: '0px 0px -10% 0px',
    once: true,
})

const faqNumber = (index: number) => String(index + 1).padStart(2, '0')
</script>

<template>
    <section ref="sectionRef" class="bg-[#f5f5f5] pt-32 pb-62">
        <div class="mx-auto max-w-7xl px-4 xl:px-0">
            <h2 :class="[
                'faq-title-enter text-center text-[#20243B]',
                isVisible && 'reveal-active',
            ]">
                <span class="font-onest text-[33px] font-medium leading-none tracking-[-0.01em]">
                    <span class="relative -top-1 text-2xl font-medium text-[#CBCBCB]">(</span>
                    Частые
                </span>

                <span
                    class="ml-2 font-playfair text-[41px] font-semibold italic leading-none tracking-[-0.01em] text-[#4252FF]">
                    вопросы
                </span>

                <span class="relative -top-1 font-onest text-2xl font-medium text-[#CBCBCB]">)</span>
            </h2>

            <div :class="[
                'faq-list-enter mx-auto mt-14',
                isVisible && 'reveal-active',
            ]">
                <Accordion type="single" collapsible class="space-y-3">
                    <AccordionItem v-for="(faq, index) in faqs" :key="faq.id" :value="String(faq.id)" :class="[
                        'faq-item-enter rounded-[14px] border-0 data-[state=open]:ring-1 data-[state=open]:ring-[#6B78FF]',
                        isVisible && 'reveal-active',
                    ]" :style="{ '--reveal-delay': `${0.18 + index * 0.08}s` }">
                        <div class="flex items-stretch gap-[2px]">
                            <div
                                class="flex min-h-[66px] shrink-0 items-center justify-center rounded-[12px] bg-white px-6 shadow-[0px_10px_24px_rgba(0,0,0,0.10)] font-onest text-[16px] font-medium leading-none text-[#A8A8A8]">
                                {{ faqNumber(index) }}
                            </div>

                            <div
                                class="relative flex min-w-0 flex-1 flex-col rounded-[12px] bg-white shadow-[0px_10px_24px_rgba(0,0,0,0.10)]">
                                <div
                                    class="pointer-events-none absolute -left-[2px] top-4 bottom-4 w-[1px] bg-[repeating-linear-gradient(to_bottom,rgba(32,36,59,0.12)_0_6px,transparent_6px_14px)]">
                                </div>

                                <AccordionTrigger
                                    class="group relative flex min-h-[66px] w-full items-center px-0 py-0 hover:no-underline [&>svg]:absolute [&>svg]:right-5 [&>svg]:top-1/2 [&>svg]:z-10 [&>svg]:size-4 [&>svg]:-translate-y-1/2 [&>svg]:text-[#A7A7B1] [&>svg]:transition-all [&[data-state=open]>svg]:rotate-180 [&[data-state=open]>svg]:text-[#4252FF]">
                                    <div class="relative flex min-w-0 flex-1 items-center pl-5 pr-16 py-5">
                                        <div class="min-w-0 flex-1 pl-4">
                                            <div
                                                class="font-onest text-[15px] font-medium leading-[1.2] text-[#20243B] transition-colors duration-200 group-data-[state=open]:text-[#4252FF]">
                                                {{ faq.question }}
                                            </div>
                                        </div>
                                    </div>
                                </AccordionTrigger>

                                <AccordionContent class="px-0 pb-0 pt-0">
                                    <div class="px-5 pb-5 pr-16 pl-9">
                                        <p class="max-w-[760px] font-onest text-[12px] leading-[1.45] text-[#8F90A0]">
                                            {{ faq.answer }}
                                        </p>
                                    </div>
                                </AccordionContent>
                            </div>
                        </div>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </section>
</template>
