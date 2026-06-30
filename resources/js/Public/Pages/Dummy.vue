<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Public/Layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    title: { type: String, default: 'Halaman' },
    description: { type: String, default: 'Halaman ini akan dibangunkan tidak lama lagi.' },
});
</script>

<template>
    <Head :title="title" />
    <main class="flex min-h-[60vh] items-center justify-center px-4 py-24">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-slate-800">{{ title }}</h1>
            <p class="mt-4 text-lg text-slate-500">{{ description }}</p>
        </div>
    </main>
</template>
