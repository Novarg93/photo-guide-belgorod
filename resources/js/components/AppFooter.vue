<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import { ArrowRight, Camera, Image, MapPin, Palette, Sparkles, UserRound, Eye, Briefcase, Heart, BookOpen, PenTool, FolderOpen, Check } from 'lucide-vue-next'
import { catalog, home } from '@/routes'
import AppLogo from './AppLogo.vue'


interface LegalPageLink {
    title: string
    slug: string
    url: string
}

interface CtaTag {
    label: string
    icon: any | null
}

interface PlacedTag extends CtaTag {
    style: Record<string, string>
}

const page = usePage()

const legalPages = computed(
    () => (page.props.legalPages ?? []) as LegalPageLink[],
)

const ctaTags: CtaTag[] = [
    { label: 'Концепция проекта', icon: null },
    { label: 'Цветовая палитра', icon: Palette },
    { label: 'Подбор идеи', icon: null },
    { label: 'Детали подготовки', icon: null },
    { label: '', icon: Image },
    { label: '', icon: Sparkles },
    { label: '', icon: Camera },
    { label: '', icon: PenTool },
    { label: '', icon: BookOpen },
    { label: 'Подготовка к съемке', icon: null },
    { label: '', icon: Check },
    { label: 'План съемки', icon: null },
    { label: 'Атмосфера', icon: null },
    { label: '', icon: UserRound },
    { label: 'Образ и одежда', icon: null },
    { label: '', icon: Eye },
    { label: 'Подбор фотографа', icon: null },
    { label: '', icon: MapPin },
    { label: 'Вдохновение кадрами', icon: null },
    { label: '', icon: Heart },
    { label: '', icon: Briefcase },
    { label: 'Выбор локации', icon: null },
    { label: 'Концепция съемки', icon: null },
    { label: 'Проверенные места', icon: null },
    { label: '', icon: FolderOpen },
]

const layoutBox = ref<HTMLElement | null>(null)
const measureRefs = ref<HTMLElement[]>([])
const placedTags = ref<PlacedTag[]>([])




const randomBetween = (min: number, max: number) => Math.random() * (max - min) + min

const shuffle = <T,>(items: T[]) => {
    const arr = [...items]
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1))
            ;[arr[i], arr[j]] = [arr[j], arr[i]]
    }
    return arr
}

const buildStyle = (x: number, y: number, rotate: number) => ({
    left: `${x}px`,
    top: `${y}px`,
    transform: `rotate(${rotate}deg)`,
})

const createGrid = (cols: number, rows: number) => {
    return Array.from({ length: rows }, () => Array.from({ length: cols }, () => false))
}

const canPlaceInGrid = (
    grid: boolean[][],
    col: number,
    row: number,
    wCells: number,
    hCells: number,
) => {
    const rows = grid.length
    const cols = grid[0]?.length ?? 0

    if (col + wCells > cols || row + hCells > rows) {
        return false
    }

    for (let y = row; y < row + hCells; y++) {
        for (let x = col; x < col + wCells; x++) {
            if (grid[y][x]) {
                return false
            }
        }
    }

    return true
}

const occupyGrid = (
    grid: boolean[][],
    col: number,
    row: number,
    wCells: number,
    hCells: number,
) => {
    for (let y = row; y < row + hCells; y++) {
        for (let x = col; x < col + wCells; x++) {
            grid[y][x] = true
        }
    }
}

const relayoutTags = async () => {
    await nextTick()

    const container = layoutBox.value
    if (!container) return

    const containerWidth = container.clientWidth
    const containerHeight = container.clientHeight

    if (!containerWidth || !containerHeight) return

    const cellSize = containerWidth >= 520 ? 28 : 24
    const cols = Math.max(1, Math.floor(containerWidth / cellSize))
    const rows = Math.max(1, Math.floor(containerHeight / cellSize))
    const grid = createGrid(cols, rows)

    const sizes = ctaTags.map((tag, index) => {
        const el = measureRefs.value[index]
        const rect = el?.getBoundingClientRect()

        const width = Math.ceil(rect?.width || (tag.label ? 140 : 40))
        const height = Math.ceil(rect?.height || 40)

        return {
            ...tag,
            originalIndex: index,
            width,
            height,
            wCells: Math.max(1, Math.ceil(width / cellSize)),
            hCells: Math.max(1, Math.ceil(height / cellSize)),
        }
    })

    const sorted = shuffle(sizes).sort((a, b) => {
        const areaA = a.wCells * a.hCells
        const areaB = b.wCells * b.hCells
        return areaB - areaA
    })

    const placed: Array<{
        originalIndex: number
        x: number
        y: number
        rotate: number
    }> = []

    for (const item of sorted) {
        let placedItem: { originalIndex: number; x: number; y: number; rotate: number } | null = null

        const scanRows = shuffle(Array.from({ length: rows }, (_, i) => i))
        const scanCols = shuffle(Array.from({ length: cols }, (_, i) => i))

        for (const row of scanRows) {
            if (placedItem) break

            for (const col of scanCols) {
                const rotate = randomBetween(-22, 22)

                if (!canPlaceInGrid(grid, col, row, item.wCells, item.hCells)) {
                    continue
                }

                occupyGrid(grid, col, row, item.wCells, item.hCells)

                const usedWidth = item.wCells * cellSize
                const usedHeight = item.hCells * cellSize

                const xPadding = Math.max(0, (usedWidth - item.width) / 2)
                const yPadding = Math.max(0, (usedHeight - item.height) / 2)

                placedItem = {
                    originalIndex: item.originalIndex,
                    x: Math.round(col * cellSize + xPadding),
                    y: Math.round(row * cellSize + yPadding),
                    rotate,
                }

                break
            }
        }

        if (!placedItem) {
            const fallbackX = Math.max(0, Math.round(randomBetween(0, Math.max(0, containerWidth - item.width))))
            const fallbackY = Math.max(0, Math.round(randomBetween(0, Math.max(0, containerHeight - item.height))))

            placedItem = {
                originalIndex: item.originalIndex,
                x: fallbackX,
                y: fallbackY,
                rotate: randomBetween(-12, 12),
            }
        }

        placed.push(placedItem)
    }

    placedTags.value = ctaTags.map((tag, index) => {
        const placedItem = placed.find((item) => item.originalIndex === index)

        return {
            ...tag,
            style: buildStyle(
                placedItem?.x ?? 0,
                placedItem?.y ?? 0,
                placedItem?.rotate ?? 0,
            ),
        }
    })
}



let resizeObserver: ResizeObserver | null = null
let resizeTimer: ReturnType<typeof setTimeout> | null = null
let relayoutInterval: ReturnType<typeof setInterval> | null = null

onMounted(async () => {
    await relayoutTags()

    resizeObserver = new ResizeObserver(() => {
        if (resizeTimer) clearTimeout(resizeTimer)
        resizeTimer = setTimeout(() => {
            relayoutTags()
        }, 120)
    })

    if (layoutBox.value) {
        resizeObserver.observe(layoutBox.value)
    }

    window.addEventListener('resize', relayoutTags)

    relayoutInterval = setInterval(() => {
        relayoutTags()
    }, 3000)
})

onBeforeUnmount(() => {
    if (resizeObserver) resizeObserver.disconnect()
    if (resizeTimer) clearTimeout(resizeTimer)
    if (relayoutInterval) clearInterval(relayoutInterval)
    window.removeEventListener('resize', relayoutTags)
})
</script>

<template>
    <footer class="relative mt-auto bg-[#14172D] pt-40">
        <div class="mx-auto max-w-7xl ">
            <div
                class="absolute w-full max-w-[90%] md:max-w-[95%] lg:max-w-[98%] xl:max-w-7xl 2xl:max-w-7xl left-[5%] -top-1/3 md:left-[3%] lg:left-[1%] xl:left-[5.5%]  md:-top-1/2 2xl:left-[16.5%] z-10 -mb-10 rounded-xl bg-white px-6 py-8 shadow-2xl md:px-8 md:py-8 lg:px-6">
                <div ref="layoutBox"
                    class="absolute inset-0 overflow-hidden rounded-[26px] xl:left-[52%] xl:rounded-r-[26px] xl:rounded-l-none">
                    <div v-for="(tag, index) in placedTags" :key="index"
                        class="absolute transition-all duration-500 ease-out" :style="tag.style">
                        <div
                            :class="tag.label
                                ? 'inline-flex items-center gap-1.5 rounded-full border border-[#4252FF]/70 bg-white/80 px-3 py-1.5 font-onest text-[12px] leading-none text-[#4252FF] sm:px-3.5 sm:py-2 sm:text-[13px] xl:gap-2 xl:border-[#4252FF] xl:bg-white xl:px-4 xl:py-2 xl:text-[15px]'
                                : 'inline-flex h-8 w-8 items-center justify-center rounded-full border border-[#4252FF]/70 bg-white/80 text-[#4252FF] sm:h-9 sm:w-9 xl:h-10 xl:w-10 xl:border-[#4252FF] xl:bg-white'">
                            <component :is="tag.icon" v-if="tag.icon" class="h-3.5 w-3.5 shrink-0 lg:h-4 lg:w-4" />
                            <span class="font-playfair text-[15px] font-semibold sm:text-[16px] lg:text-[19px]"
                                v-if="tag.label">
                                {{ tag.label }}
                            </span>
                        </div>
                    </div>

                    <div class="pointer-events-none absolute inset-0 opacity-0">
                        <div v-for="(tag, index) in ctaTags" :key="`measure-${index}`"
                            :ref="(el) => { if (el) measureRefs[index] = el as HTMLElement }"
                            class="absolute left-0 top-0">
                            <div
                                :class="tag.label
                                    ? 'inline-flex items-center gap-1.5 rounded-full border border-[#4252FF]/70 bg-white/80 px-3 py-1.5 font-onest text-[12px] leading-none text-[#4252FF] sm:px-3.5 sm:py-2 sm:text-[13px] xl:gap-2 xl:border-[#4252FF] xl:bg-white xl:px-4 xl:py-2 xl:text-[15px]'
                                    : 'inline-flex h-8 w-8 items-center justify-center rounded-full border border-[#4252FF]/70 bg-white/80 text-[#4252FF] sm:h-9 sm:w-9 xl:h-10 xl:w-10 xl:border-[#4252FF] xl:bg-white'">
                                <component :is="tag.icon" v-if="tag.icon" class="h-3.5 w-3.5 shrink-0 xl:h-4 xl:w-4" />
                                <span class="font-playfair text-[15px] font-semibold sm:text-[16px] xl:text-[19px]"
                                    v-if="tag.label">
                                    {{ tag.label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="pointer-events-none absolute inset-0 rounded-[26px] bg-[linear-gradient(180deg,rgba(255,255,255,0.35)_0%,rgba(255,255,255,0.18)_100%)] xl:hidden">
                </div>

                <div class="relative z-10 grid gap-8 xl:grid-cols-[1.1fr_0.9fr] xl:items-stretch">
                    <div class="flex min-h-[280px] flex-col justify-center lg:items-center xl:items-start sm:min-h-[300px] xl:min-h-[320px]">
                        <div class="inline-block max-w-[700px] rounded-[22px] bg-white/72 p-4 text-center backdrop-blur-[2px] sm:p-5 xl:rounded-none xl:bg-transparent xl:backdrop-blur-[0px] xl:p-0 xl:text-left xl:backdrop-blur-0">
                            <h2
                                class="font-onest text-[24px] font-medium leading-[1.02] tracking-[-0.01em] text-[#20243B] drop-shadow-[0_2px_10px_rgba(255,255,255,0.95)] sm:text-[28px] md:text-[30px] xl:text-[33px]">
                                Найдите идею фотосессии, изучите референсы и создайте бриф, который поможет фотографу
                                реализовать
                                <span class="text-[#8E93A8]">задумку</span>
                            </h2>

                            <div class="mt-6 flex justify-center sm:mt-8 xl:justify-start">
                                <Link :href="catalog()"
                                    class="inline-flex items-center gap-2 rounded-full bg-[radial-gradient(73.53%_123.27%_at_50%_100%,rgba(59,70,255,0.7)_0%,#3B46FF_100%)] px-5 py-3 font-onest text-[14px] font-medium text-white shadow-[0px_10px_24px_rgba(59,70,255,0.35),inset_0px_6px_14px_rgba(255,255,255,0.35)] transition duration-200 hover:-translate-y-[1px] sm:px-6 sm:py-4 sm:text-[16px]">
                                    Попробовать бесплатно
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="hidden xl:block"></div>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-[#14172D] pt-20">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(66,82,255,0.12),transparent_55%)]">
            </div>

            <div class="relative mx-auto max-w-3/4 px-4 pb-6">
                <div
                    class="flex flex-col gap-6 border-t border-white/10 pt-10 lg:flex-row lg:items-end lg:justify-between">
                    <nav class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-white/90">
                        <Link :href="catalog()" class="transition hover:text-white">
                            Каталог
                        </Link>
                        <Link :href="home()" class="transition hover:text-white">
                            Стили
                        </Link>
                        <Link :href="catalog()" class="transition hover:text-white">
                            Локации
                        </Link>
                        <Link :href="catalog()" class="transition hover:text-white">
                            Фотографы
                        </Link>
                        <Link :href="catalog()" class="transition hover:text-white">
                            Создать бриф
                        </Link>
                    </nav>

                    <div class="flex justify-center lg:justify-center">
                        <Link :href="home()"
                           >
                            <AppLogo class="" />
                        </Link>
                    </div>

                    <div
                        class="flex flex-wrap items-center justify-start gap-x-4 gap-y-2 text-sm text-white/75 lg:justify-end">
                        <template v-for="legalPage in legalPages" :key="legalPage.slug">
                            <Link :href="legalPage.url" class="transition hover:text-white">
                                {{ legalPage.title }}
                            </Link>
                        </template>
                        <span>© Все права защищены, 2026</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>