<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import { computed } from 'vue';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

interface LocationPageData {
    name: string;
    slug: string;
    description: string | null;
    image_url: string;
    category: string | null;
    example_photos: string[];
    url: string;
}

const props = defineProps<{
    location: LocationPageData;
    metaTitle: string;
    metaDescription: string;
}>();

const jsonLd = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Place',
        name: props.location.name,
        description: props.metaDescription,
        image: props.location.image_url,
        url: props.location.url,
        address: {
            '@type': 'PostalAddress',
            addressLocality: 'Belgorod',
            addressCountry: 'RU',
        },
    }),
);

useHead(() => ({
  title: props.metaTitle,
  meta: [
    { key: 'description', name: 'description', content: props.metaDescription },

    { key: 'og:title', property: 'og:title', content: props.metaTitle },
    { key: 'og:description', property: 'og:description', content: props.metaDescription },
    { key: 'og:type', property: 'og:type', content: 'website' },
    { key: 'og:image', property: 'og:image', content: props.location.image_url },
    { key: 'og:url', property: 'og:url', content: props.location.url },

    { key: 'twitter:card', name: 'twitter:card', content: 'summary_large_image' },
  ],
  link: [
    { key: 'canonical', rel: 'canonical', href: props.location.url },
  ],
  script: [
    { key: 'location-jsonld', type: 'application/ld+json', children: jsonLd.value },
  ],
}));
</script>

<template>
    <AppHeaderLayout>
        <section class="mx-auto w-full max-w-7xl py-8">
            <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500">
                <Link href="/locations" class="transition hover:text-zinc-900">Locations</Link>
                <span>/</span>
                <span class="text-zinc-700">{{ location.name }}</span>
            </div>

            <div class="mt-6 grid gap-8 lg:grid-cols-[1.3fr_1fr]">
                <article class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
                    <div class="aspect-16/10 overflow-hidden bg-zinc-100">
                        <img :src="location.image_url" :alt="location.name" class="h-full w-full object-cover" />
                    </div>

                    <div class="space-y-3 p-6">
                        <h1 class="text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                            {{ location.name }}
                        </h1>

                        <p v-if="location.category" class="text-sm font-medium uppercase tracking-wide text-zinc-500">
                            {{ location.category }}
                        </p>

                        <p class="text-base leading-relaxed text-zinc-700">
                            {{
                                location.description ||
                                `Location ${location.name} is suitable for photo sessions in Belgorod. Browse examples and choose a matching style.`
                            }}
                        </p>
                    </div>
                </article>

                <aside class="rounded-3xl border border-zinc-200 bg-zinc-50 p-6">
                    <h2 class="text-xl font-semibold text-zinc-900">Why this location works for a photo session</h2>
                    <ul class="mt-4 space-y-3 text-sm leading-relaxed text-zinc-700">
                        <li>Supports multiple scenarios: portraits, love story, family sessions.</li>
                        <li>You can choose style and references from the examples below.</li>
                        <li>The page is optimized for Belgorod local search queries.</li>
                    </ul>
                </aside>
            </div>

            <div class="mt-10">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900">Photo session examples at this location</h2>

                <div v-if="location.example_photos.length" class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="(photoUrl, index) in location.example_photos"
                        :key="`${location.slug}-${index}`"
                        class="overflow-hidden rounded-2xl border border-zinc-200 bg-white"
                    >
                        <div class="aspect-16/10 overflow-hidden bg-zinc-100">
                            <img
                                :src="photoUrl"
                                :alt="`${location.name} photo session example ${index + 1}`"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            />
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="mt-6 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-zinc-600"
                >
                    No examples are available for this location yet.
                </div>
            </div>
        </section>
    </AppHeaderLayout>
</template>
