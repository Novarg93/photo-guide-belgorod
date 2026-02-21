<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

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

        <section class="mx-auto w-full max-w-4xl py-8">
            <h1 class="text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">
                Copyright and photo sources
            </h1>

            <p class="mt-4 text-sm leading-relaxed text-zinc-700">
                Photos in this project are moderated and can come from three source types:
                own content, permission-based content, or stock content.
                New uploads are inactive by default until reviewed.
            </p>

            <p class="mt-4 text-sm leading-relaxed text-zinc-700">
                If you are a copyright owner and want content removed, contact:
                rights@example.com
            </p>

            <div class="mt-6">
                <Link href="/" class="text-sm font-medium text-zinc-700 underline underline-offset-4">
                    Back to home
                </Link>
            </div>
        </section>
    </AppHeaderLayout>
</template>
