<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { catalog, home } from '@/routes';
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
                <Separator orientation="vertical" class="h-5" />
                <Button variant="ghost" size="sm" as-child>
                    <a href="/admin">Admin</a>
                </Button>
            </nav>
        </div>
    </footer>
</template>
