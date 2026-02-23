<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useHead } from '@vueuse/head';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

const props = defineProps<{
    metaTitle: string;
    metaDescription: string;
}>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
});

useHead(() => ({
    title: props.metaTitle,
    meta: [{ key: 'description', name: 'description', content: props.metaDescription }],
}));

const submit = (): void => {
    form.post('/contact-us', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppHeaderLayout>
        <section class="mx-auto w-full max-w-4xl py-10 md:py-14">
            <h1 class="text-3xl font-semibold tracking-tight text-zinc-900 md:text-4xl">Contact Us</h1>
            <p class="mt-3 max-w-2xl text-zinc-600">
                Send your request and we will respond as soon as possible.
            </p>

            <form class="mt-8 space-y-4 rounded-2xl border border-zinc-200 bg-white p-6" @submit.prevent="submit">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-zinc-800">Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-zinc-500"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-zinc-800">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-zinc-500"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="phone" class="mb-1 block text-sm font-medium text-zinc-800">Phone (optional)</label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-zinc-500"
                        />
                        <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                    </div>
                </div>

                <div>
                    <label for="message" class="mb-1 block text-sm font-medium text-zinc-800">Message</label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        rows="6"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm outline-none transition focus:border-zinc-500"
                    />
                    <p v-if="form.errors.message" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-700">Your request has been sent.</p>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Sending...' : 'Send request' }}
                    </button>
                </div>
            </form>
        </section>
    </AppHeaderLayout>
</template>
