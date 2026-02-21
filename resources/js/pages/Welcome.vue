<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { catalog } from '@/routes';

defineProps<{
    metaTitle: string;
    metaDescription: string;
}>();
</script>

<template>
    <Head :title="metaTitle">
        <meta head-key="description" name="description" :content="metaDescription" />
    </Head>

    <main class="min-h-screen bg-[linear-gradient(180deg,#fff8ef_0%,#fff_55%,#f8fbff_100%)] text-zinc-900">
        <section class="mx-auto flex min-h-screen w-full max-w-5xl flex-col items-start justify-center px-6 py-16">
            <p class="mb-4 inline-flex rounded-full border border-zinc-200 bg-white/70 px-4 py-1 text-xs font-medium uppercase tracking-[0.2em] text-zinc-500">
                Photo Guide Belgorod
            </p>

            <h1 class="max-w-3xl text-4xl leading-tight font-semibold tracking-tight text-zinc-950 md:text-6xl">
                Choose your photo session style in Belgorod in minutes
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-relaxed text-zinc-600 md:text-lg">
                Browse categories, pick the right session type, and move to the next planning step.
            </p>

            <Link
                :href="catalog()"
                class="mt-10 inline-flex items-center rounded-xl bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700"
            >
                Choose a photo session
            </Link>
        </section>
    </main>
</template>
