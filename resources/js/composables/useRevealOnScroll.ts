import { onBeforeUnmount, onMounted, ref } from 'vue'

type UseRevealOnScrollOptions = {
    threshold?: number
    rootMargin?: string
    once?: boolean
}

export function useRevealOnScroll(options: UseRevealOnScrollOptions = {}) {
    const {
        threshold = 0.2,
        rootMargin = '0px 0px -10% 0px',
        once = true,
    } = options

    const sectionRef = ref<HTMLElement | null>(null)
    const isVisible = ref(false)

    let observer: IntersectionObserver | null = null

    onMounted(() => {
        if (!sectionRef.value) return

        observer = new IntersectionObserver(
            (entries) => {
                const entry = entries[0]

                if (!entry) return

                if (entry.isIntersecting) {
                    isVisible.value = true

                    if (once) {
                        observer?.disconnect()
                        observer = null
                    }

                    return
                }

                if (!once) {
                    isVisible.value = false
                }
            },
            {
                threshold,
                rootMargin,
            },
        )

        observer.observe(sectionRef.value)
    })

    onBeforeUnmount(() => {
        observer?.disconnect()
        observer = null
    })

    return {
        sectionRef,
        isVisible,
    }
}