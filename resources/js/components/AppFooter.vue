<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { catalog, home } from '@/routes';

interface LegalPageLink {
    title: string;
    slug: string;
    url: string;
}

const page = usePage();
const legalPages = computed(
    () => (page.props.legalPages ?? []) as LegalPageLink[],
);
</script>

<template>
    <footer class="mt-auto border-t border-sidebar-border/70 bg-background/95">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 py-6 md:flex-row md:items-center md:justify-between">
            <p class="text-sm text-muted-foreground">
                Photo Guide Belgorod. Footer placeholder text.
            </p>

            <nav class="flex items-center gap-1">
                <Button variant="ghost" size="sm" as-child>
                    <Link :href="home()">Home</Link>
                </Button>
                <Separator orientation="vertical" class="h-5" />
                <Button variant="ghost" size="sm" as-child>
                    <Link :href="catalog()">Catalog</Link>
                </Button>
                <template v-for="legalPage in legalPages" :key="legalPage.slug">
                    <Separator orientation="vertical" class="h-5" />
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="legalPage.url">{{ legalPage.title }}</Link>
                    </Button>
                </template>
                <Separator orientation="vertical" class="h-5" />
                <Button variant="ghost" size="sm" as-child>
                    <a href="/admin">Admin</a>
                </Button>
            </nav>
        </div>
    </footer>
</template>
