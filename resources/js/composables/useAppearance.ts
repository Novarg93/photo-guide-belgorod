import { onMounted } from 'vue';

export function initializeTheme(): void {
    if (typeof window === 'undefined') {
        return;
    }

    document.documentElement.classList.remove('dark');
}

export function useAppearance() {
    onMounted(() => {
        document.documentElement.classList.remove('dark');
    });

    return {
        appearance: 'light',
        resolvedAppearance: 'light',
        updateAppearance: () => {
            // no-op
        },
    };
}