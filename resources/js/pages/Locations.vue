<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

interface LocationCard {
    id: number;
    name: string;
    category: string | null;
    image_url: string;
}

defineProps<{
    locations: LocationCard[];
    metaTitle: string;
    metaDescription: string;
}>();
</script>

<template>
    <AppHeaderLayout>
        <Head :title="metaTitle">
            <meta head-key="description" name="description" :content="metaDescription" />
        </Head>

        <section class="mx-auto w-full max-w-7xl py-8">
            <Link href="/catalog" class="text-sm text-zinc-500 transition hover:text-zinc-900">Back to catalog</Link>

            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                Locations Catalog
            </h1>

            <p class="mt-3 max-w-2xl text-zinc-600">
                All available locations for photo sessions in Belgorod.
            </p>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="location in locations"
                    :key="location.id"
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white"
                >
                    <div class="aspect-16/10 overflow-hidden bg-zinc-100">
                        <img
                            :src="location.image_url"
                            :alt="location.name"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                    </div>

                    <div class="space-y-1 p-4">
                        <h2 class="text-lg font-semibold text-zinc-900">{{ location.name }}</h2>
                        <p v-if="location.category" class="text-sm text-zinc-600">Category: {{ location.category }}</p>
                    </div>
                </article>
            </div>

            <div
                v-if="locations.length === 0"
                class="mt-8 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600"
            >
                Locations catalog is empty.
            </div>
        </section>
    </AppHeaderLayout>
</template>
