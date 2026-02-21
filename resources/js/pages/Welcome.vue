<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { Button } from '@/components/ui/button';
import { catalog } from '@/routes';

defineProps<{
    metaTitle: string;
    metaDescription: string;
}>();
</script>

<template>
    <AppHeaderLayout>
        <Head :title="metaTitle">
            <meta head-key="description" name="description" :content="metaDescription" />
        </Head>

        <section class="mx-auto w-full max-w-5xl py-12 md:py-20">
            <p class="mb-4 inline-flex rounded-full border border-zinc-200 bg-white/70 px-4 py-1 text-xs font-medium uppercase tracking-[0.2em] text-zinc-500">
                Photo Guide Belgorod
            </p>

            <h1 class="max-w-3xl text-4xl leading-tight font-semibold tracking-tight text-zinc-950 md:text-6xl">
                Choose your photo session style in Belgorod in minutes
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-relaxed text-zinc-600 md:text-lg">
                Browse categories, pick the right session type, and move to the next planning step.
            </p>

            <div class="mt-10">
                <Button as-child size="lg">
                    <Link :href="catalog()">Choose a photo session</Link>
                </Button>
            </div>
        </section>
    </AppHeaderLayout>
</template>
