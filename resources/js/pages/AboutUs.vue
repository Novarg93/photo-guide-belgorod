<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

const props = defineProps<{
    metaTitle: string;
    metaDescription: string;
}>();

useHead(() => ({
    title: props.metaTitle,
    meta: [{ key: 'description', name: 'description', content: props.metaDescription }],
}));
</script>

<template>
    <AppHeaderLayout>
        <section class="mx-auto w-full max-w-5xl py-10 md:py-14">
            <Link href="/" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to home</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">About Us</h1>

            <p class="mt-4 max-w-3xl text-zinc-600">
                Photo Guide Belgorod is a local content project that helps people plan photo sessions faster and with more confidence.
            </p>
        </section>

        <section class="mx-auto w-full max-w-5xl pb-8 md:pb-10">
            <div class="grid gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h2 class="text-lg font-semibold text-zinc-900">Why this project exists</h2>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600">
                        We created this project to organize local photo session knowledge in one place: categories, references, locations,
                        photographers, and planning tips.
                    </p>
                </article>

                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h2 class="text-lg font-semibold text-zinc-900">Problem we solve</h2>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600">
                        Most people spend too much time searching across chats and social media. We reduce this chaos with structured filters,
                        ready examples, and a clear brief flow.
                    </p>
                </article>

                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <h2 class="text-lg font-semibold text-zinc-900">Our plans</h2>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600">
                        Next steps are deeper content coverage, better filtering quality, richer local guides, and more practical tools for
                        faster communication between clients and photographers.
                    </p>
                </article>
            </div>
        </section>
    </AppHeaderLayout>
</template>
