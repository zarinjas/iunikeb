<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Layout, Pencil } from 'lucide-vue-next';
import { Button } from '@/Shared/Components/ui/button';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';

defineProps({
    sections: { type: Array, required: true },
});

const sectionLabels = {
    hero: 'Hero Slider',
    stats: 'Statistik',
    services: 'Perkhidmatan',
    benefit: 'Manfaat Ahli',
    business: 'Perniagaan',
    promotion: 'Promosi',
    membership: 'Keahlian',
    footer: 'Footer',
};
</script>

<template>
    <Head title="Halaman Utama" />

    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Pengurusan Halaman Utama</h1>
                <p class="mt-2 text-slate-500">Kemas kini kandungan setiap seksyen halaman utama.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <Link
                    v-for="section in sections"
                    :key="section.id"
                    :href="`/admin/frontpage/sections/${section.key}`"
                    class="group flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-teal-200 hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                                <Layout class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800">{{ sectionLabels[section.key] || section.key }}</h3>
                                <p class="text-xs text-slate-400">{{ section.items_count }} item</p>
                            </div>
                        </div>
                        <Pencil class="h-4 w-4 text-slate-300 transition group-hover:text-teal-500" />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <ArrowRight class="h-4 w-4 transition group-hover:translate-x-0.5" />
                        <span>Edit seksyen</span>
                    </div>
                </Link>
            </div>

            <div class="flex gap-4">
                <a href="/" target="_blank" class="inline-flex items-center gap-2 text-sm text-teal-600 hover:text-teal-700">
                    Lihat halaman utama
                    <ArrowRight class="h-4 w-4" />
                </a>
            </div>
        </div>
    </AdminLayout>
</template>
