<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

interface LocationCard {
    id: number;
    name: string;
    slug: string;
    category: string | null;
    image_url: string;
    description: string | null;
    url: string;
}

const props = defineProps<{
    locations: LocationCard[];
    metaTitle: string;
    metaDescription: string;
}>();

useHead(() => ({
    title: props.metaTitle,
    meta: [
        { key: 'description', name: 'description', content: props.metaDescription },
    ],
}));
</script>

<template>
    <AppHeaderLayout>
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
                    <Link :href="location.url" class="block">
                        <div class="aspect-16/10 overflow-hidden bg-zinc-100">
                            <img
                                :src="location.image_url"
                                :alt="location.name"
                                class="h-full w-full object-cover transition duration-300 hover:scale-[1.02]"
                                loading="lazy"
                            />
                        </div>

                        <div class="space-y-2 p-4">
                            <h2 class="text-lg font-semibold text-zinc-900">{{ location.name }}</h2>
                            <p v-if="location.category" class="text-sm text-zinc-600">
                                Category: {{ location.category }}
                            </p>
                            <p v-if="location.description" class="line-clamp-2 text-sm text-zinc-500">
                                {{ location.description }}
                            </p>
                        </div>
                    </Link>
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
