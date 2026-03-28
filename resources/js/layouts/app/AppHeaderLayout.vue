<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell class="relative" variant="header">
        <AppHeader class="absolute inset-x-0 top-0 z-50" :breadcrumbs="breadcrumbs" />
        <AppContent >
            <slot />
        </AppContent>
        <AppFooter />
    </AppShell>
</template>
