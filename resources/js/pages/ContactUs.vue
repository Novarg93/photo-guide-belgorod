<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { useHead } from '@vueuse/head'
import { ArrowRight, Clock3, Mail, MapPinned, MessageCircle, Sparkles } from 'lucide-vue-next'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'

const props = defineProps<{
    metaTitle: string;
    metaDescription: string;
}>()

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
})

useHead(() => ({
    title: props.metaTitle,
    meta: [{ key: 'description', name: 'description', content: props.metaDescription }],
}))

const submit = (): void => {
    form.post('/contact-us', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    })
}

const contactHighlights = [
    {
        icon: Mail,
        title: 'О чем можно спросить',
        description: 'Выбор категории, идеи для локаций, подбор референсов или как перейти от просмотра к понятному брифу.',
    },
    {
        icon: Clock3,
        title: 'Когда писать',
        description: 'Когда вы не можете выбрать между несколькими вариантами съемки и нужен понятный следующий шаг.',
    },
    {
        icon: MapPinned,
        title: 'Локальный фокус',
        description: 'Проект ориентирован на Белгород, поэтому рекомендации остаются практичными и привязанными к городу.',
    },
] as const
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
                        <a
                            href="/"
                            class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/80 px-4 py-2 text-sm text-[#20243B] shadow-[0px_10px_24px_rgba(0,0,0,0.06)] backdrop-blur-sm"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <Sparkles class="h-3.5 w-3.5" />
                            </span>
                            <span>На главную</span>
                        </a>

                        <div class="mt-7 grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-end">
                            <div>
                                <h1 class="text-[#20243B]">
                                    <span class="font-onest text-[38px] font-medium leading-none tracking-[-0.02em] md:text-[54px]">
                                        Связаться
                                    </span>
                                    <span class="ml-2 font-playfair text-[44px] font-semibold italic leading-none tracking-[-0.02em] text-[#4252FF] md:text-[64px]">
                                        с нами
                                    </span>
                                </h1>

                                <p class="mt-5 max-w-3xl text-sm leading-6 text-[#5C6079] md:text-base">
                                    Отправьте заявку, и мы ответим вам как можно скорее.
                                </p>
                            </div>

                            <div class="rounded-[24px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]">
                                <p class="font-onest text-sm font-medium text-[#A0A3B8]">Подходит для</p>
                                <div class="mt-3 space-y-3">
                                    <div class="rounded-[18px] bg-[#F7F8FF] px-4 py-3 text-sm leading-6 text-[#303651]">
                                        Вопросов по категориям, фильтрам, локальным референсам и планированию фотосессии в Белгороде.
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <div class="inline-flex items-center gap-2 rounded-full border border-[#D9DCF3] bg-white px-3 py-1.5 text-xs font-medium text-[#4252FF]">
                                            <Mail class="h-3.5 w-3.5" />
                                            Ответ по email
                                        </div>
                                        <div class="inline-flex items-center gap-2 rounded-full border border-[#D9DCF3] bg-white px-3 py-1.5 text-xs font-medium text-[#4252FF]">
                                            <MessageCircle class="h-3.5 w-3.5" />
                                            Свободный формат запроса
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 xl:grid-cols-[1.1fr_0.72fr] xl:items-start">
                    <form
                        class="rounded-[32px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)] md:p-6"
                        @submit.prevent="submit"
                    >
                        <div class="contact-form-shell rounded-[26px] bg-[#F7F8FF] p-4 md:p-5">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="contact-field">
                                    <label for="name" class="contact-label">Имя</label>
                                    <input id="name" v-model="form.name" type="text" class="contact-input">
                                    <p v-if="form.errors.name" class="contact-error">{{ form.errors.name }}</p>
                                </div>

                                <div class="contact-field">
                                    <label for="email" class="contact-label">Email</label>
                                    <input id="email" v-model="form.email" type="email" class="contact-input">
                                    <p v-if="form.errors.email" class="contact-error">{{ form.errors.email }}</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="contact-field">
                                    <label for="phone" class="contact-label">Телефон (необязательно)</label>
                                    <input id="phone" v-model="form.phone" type="text" class="contact-input">
                                    <p v-if="form.errors.phone" class="contact-error">{{ form.errors.phone }}</p>
                                </div>
                            </div>

                            <div class="contact-message-wrap mt-4">
                                <label for="message" class="contact-label">Сообщение</label>
                                <p class="mt-1 text-sm text-[#8A8FAF]">
                                    Опишите детали, чтобы мы лучше поняли ваш запрос.
                                </p>

                                <div class="contact-message-card mt-3 rounded-[24px] bg-white p-3 md:p-4">
                                    <textarea id="message" v-model="form.message" rows="7" class="contact-textarea" />
                                </div>
                                <p v-if="form.errors.message" class="contact-error mt-2">{{ form.errors.message }}</p>
                            </div>
                        </div>

                        <div class="contact-actions mt-5 flex flex-col gap-4 rounded-[24px] border border-[#E2E5F6] bg-white/70 p-4 md:flex-row md:items-center md:justify-between">
                            <p v-if="form.recentlySuccessful" class="text-sm text-green-700">
                                Ваша заявка отправлена.
                            </p>
                            <div v-else class="text-sm text-[#8A8FAF]">
                                Опишите ваш запрос и добавьте любые полезные детали.
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="contact-submit button-primary inline-flex items-center justify-center gap-2 px-6 py-4"
                            >
                                <span>{{ form.processing ? 'Отправка...' : 'Отправить заявку' }}</span>
                                <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>
                    </form>

                    <aside class="space-y-4">
                        <article
                            v-for="(item, index) in contactHighlights"
                            :key="item.title"
                            class="contact-info-card rounded-[28px] bg-white p-5 shadow-[0px_18px_40px_rgba(20,23,45,0.08)]"
                        >
                            <div class="flex items-start gap-4">
                                <div class="contact-info-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-[16px] bg-primary text-white">
                                    <component :is="item.icon" class="h-5 w-5" />
                                </div>

                                <div>
                                    <h2 class="font-onest text-[22px] font-medium leading-none text-[#20243B]">
                                        {{ item.title }}
                                    </h2>
                                    <p class="mt-3 text-sm leading-6 text-[#5C6079]">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    </aside>
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>