<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Camera, CircleHelp, LayoutGrid, Mail, MapPin, Menu } from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { getInitials } from '@/composables/useInitials';
import {
    resetHeaderNavPill,
    setHeaderNavPillReady,
    setHeaderNavPillStyle,
    useHeaderNavPill,
} from '@/composables/useHeaderNavPill';
import { catalog, dashboard, home, locations, login, register } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';

const { sliderStyle, isSliderReady } = useHeaderNavPill();

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const isAuthenticated = computed(() => Boolean(auth.value?.user));
const { isCurrentUrl, whenCurrentUrl } = useCurrentUrl();

const navMenuRef = ref<HTMLElement | null>(null);

const activeItemStyles =
    'text-white bg-[radial-gradient(73.53%_100%_at_50.28%_100%,rgba(20,23,45,0.7)_0%,#14172D_33.25%,#14172D_100%)] shadow-[inset_0px_4px_9px_-2px_rgba(255,255,255,0.5)] pointer-events-none';

const mainNavItems: NavItem[] = [
    {
        title: 'Каталог',
        href: catalog(),
        icon: LayoutGrid,
    },
    {
        title: 'О нас',
        href: '/about-us',
        icon: CircleHelp,
    },
    {
        title: 'Контакты',
        href: '/contact-us',
        icon: Mail,
    },
    {
        title: 'Локации',
        href: locations(),
        icon: MapPin,
    },
    {
        title: 'Фотографы',
        href: '/photographers',
        icon: Camera,
    },
    {
        title: 'Блог',
        href: '/blogs',
        icon: BookOpen,
    },
];

const activeIndex = computed(() =>
    mainNavItems.findIndex((item) => isCurrentUrl(item.href)),
);

const updateSlider = async (index?: number | null) => {
    await nextTick();

    const root = navMenuRef.value;

    if (!root) {
        return;
    }

    const targetIndex = index ?? activeIndex.value;

    if (targetIndex == null || targetIndex < 0) {
        resetHeaderNavPill();

        return;
    }

    const items = root.querySelectorAll('[data-nav-item]');
    const target = items[targetIndex] as HTMLElement | undefined;

    if (!target) {
        return;
    }

    const rootRect = root.getBoundingClientRect();
    const targetRect = target.getBoundingClientRect();
    const left = targetRect.left - rootRect.left;

    setHeaderNavPillStyle({
        width: `${targetRect.width}px`,
        height: `${targetRect.height}px`,
        transform: `translateX(${left}px)`,
        opacity: '1',
    });

    if (!isSliderReady.value) {
        requestAnimationFrame(() => {
            setHeaderNavPillReady(true);
        });
    }
};

onMounted(() => {
    if (activeIndex.value == null || activeIndex.value < 0) {
        resetHeaderNavPill();

        return;
    }

    void updateSlider(activeIndex.value);
});

watch(activeIndex, (value) => {
    if (value == null || value < 0) {
        resetHeaderNavPill();

        return;
    }

    void updateSlider(value);
});
</script>

<template>
    <div>
        <div>
            <div class="mx-auto flex items-center px-4 py-7 md:max-w-7xl">
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button variant="ghost" size="icon" class="mr-2 h-9 w-9">
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-full p-6">
                            <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogoIcon class="size-6 fill-current text-black dark:text-white" />
                            </SheetHeader>
                            <div class="flex w-full flex-col items-center justify-between space-y-4 py-6 text-center">
                                <nav class="w-full items-center justify-center space-y-1 text-center">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="flex items-center justify-center gap-x-3 rounded-lg px-3 py-2 text-center text-[15px] hover:bg-accent"
                                        :class="whenCurrentUrl(item.href, activeItemStyles)"
                                    >
                                        {{ item.title }}
                                    </Link>
                                    <div class="space-y-1">
                                        <template v-if="!isAuthenticated">
                                            <Link
                                                :href="register()"
                                                class="button-secondary flex items-center justify-center gap-x-3 rounded-4xl px-6 py-4 text-center text-[15px] font-medium hover:bg-accent"
                                            >
                                                Регистрация
                                            </Link>
                                            <Link
                                                :href="login()"
                                                class="flex items-center justify-center gap-x-3 rounded-lg px-3 py-2 text-center text-[15px] font-medium hover:bg-accent"
                                            >
                                                Вход
                                            </Link>
                                        </template>
                                        <Link
                                            v-else
                                            :href="dashboard()"
                                            class="flex items-center justify-center gap-x-3 rounded-lg px-3 py-2 text-center text-[15px] font-medium hover:bg-accent"
                                        >
                                            Личный Кабинет
                                        </Link>
                                    </div>
                                </nav>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link :href="home()" class="flex items-center gap-x-2">
                    <AppLogo />
                </Link>

                <div class="hidden h-full items-center lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 h-fit rounded-4xl bg-[#e5e5e5] px-0 py-0">
                        <div ref="navMenuRef" class="relative">
                            <NavigationMenuList class="relative flex h-full items-stretch">
                                <div
                                    :class="[
                                        'pointer-events-none absolute left-0 top-0 z-0 rounded-4xl',
                                        isSliderReady
                                            ? 'transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]'
                                            : '',
                                    ]"
                                    :style="sliderStyle"
                                >
                                    <div class="button-secondary h-full w-full"></div>
                                </div>

                                <NavigationMenuItem
                                    v-for="(item, index) in mainNavItems"
                                    :key="index"
                                    data-nav-item
                                    class="relative z-10 flex items-center"
                                >
                                    <Link
                                        :href="item.href"
                                        :class="[
                                            navigationMenuTriggerStyle(),
                                            '!rounded-4xl !px-6 !py-6 flex items-center text-[15px] transition-all duration-200',
                                            isCurrentUrl(item.href)
                                                ? '!text-white pointer-events-none'
                                                : 'button-primary-hover text-muted-foreground',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            v-if="item.icon && isCurrentUrl(item.href)"
                                            class="mr-2 h-4 w-4 shrink-0"
                                        />
                                        <span>{{ item.title }}</span>
                                    </Link>
                                </NavigationMenuItem>
                            </NavigationMenuList>
                        </div>
                    </NavigationMenu>
                </div>

                <div class="ml-auto flex items-center space-x-2">
                    <DropdownMenu v-if="isAuthenticated">
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage
                                        v-if="auth.user?.avatar"
                                        :src="auth.user.avatar"
                                        :alt="auth.user?.name"
                                    />
                                    <AvatarFallback
                                        class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <div v-else class="flex items-center gap-2">
                        <Button class="button-primary-hover rounded-4xl px-6 py-6 text-[15px]" variant="ghost" as-child>
                            <Link :href="login()">Вход</Link>
                        </Button>
                        <Button class="button-secondary rounded-4xl px-6 py-6 text-[15px]" variant="ghost" as-child>
                            <Link :href="register()">Регистрация</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-b border-sidebar-border/70">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
