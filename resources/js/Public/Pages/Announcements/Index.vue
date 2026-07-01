<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Megaphone, Pin, Zap } from 'lucide-vue-next';
import PublicLayout from '@/Public/Layouts/PublicLayout.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';

defineProps({
    announcements: { type: Object, required: true },
});

const priorityStyle = (priority) => {
    switch (priority) {
        case 'penting': return { icon: Zap, bg: 'from-amber-50 to-white', border: 'border-amber-200', iconBg: 'bg-amber-100 text-amber-600', badge: 'bg-amber-100 text-amber-700' };
        case 'segera': return { icon: Zap, bg: 'from-red-50 to-white', border: 'border-red-200', iconBg: 'bg-red-100 text-red-600', badge: 'bg-red-100 text-red-700' };
        default: return { icon: Megaphone, bg: 'from-teal-50/50 to-white', border: 'border-teal-100', iconBg: 'bg-teal-100 text-teal-600', badge: 'bg-teal-100 text-teal-700' };
    }
};
</script>

<template>
    <Head title="Pengumuman" />

    <PublicLayout>
        <section class="py-16">
            <div class="mx-auto max-w-4xl px-4">
                <div class="mb-12 text-center">
                    <h1 class="text-3xl font-bold text-slate-950">Pengumuman</h1>
                    <p class="mt-3 text-slate-500">Hebahan dan makluman terkini dari koperasi.</p>
                </div>

                <div v-if="announcements.data.length" class="space-y-4">
                    <Link
                        v-for="a in announcements.data"
                        :key="a.id"
                        :href="a.show_url"
                        class="group block rounded-2xl border bg-gradient-to-r p-5 shadow-sm transition hover:shadow-md"
                        :class="[priorityStyle(a.priority).bg, priorityStyle(a.priority).border]"
                    >
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" :class="priorityStyle(a.priority).iconBg">
                                <component :is="priorityStyle(a.priority).icon" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-semibold text-slate-950 group-hover:text-teal-700 transition-colors">{{ a.title }}</h3>
                                    <span
                                        v-if="a.is_pinned"
                                        class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700"
                                    >
                                        <Pin class="h-3 w-3" />
                                        Dipin
                                    </span>
                                    <span
                                        v-if="a.priority !== 'normal'"
                                        class="rounded-full px-2 py-0.5 text-[11px] font-medium"
                                        :class="priorityStyle(a.priority).badge"
                                    >
                                        {{ a.priority === 'penting' ? 'Penting' : 'Segera' }}
                                    </span>
                                </div>
                                <p class="mt-1.5 line-clamp-2 text-sm text-slate-500">{{ a.summary || 'Tiada ringkasan.' }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ a.published_at }}</p>
                            </div>
                            <ChevronRight class="mt-3 h-4 w-4 shrink-0 text-slate-300 group-hover:text-teal-500 transition-colors" />
                        </div>
                    </Link>
                </div>

                <EmptyState
                    v-else
                    title="Tiada pengumuman buat masa ini."
                    description="Sila semak semula kemudian."
                />

                <div v-if="announcements.links?.length > 3" class="mt-8 flex justify-center gap-2">
                    <component
                        :is="link.url ? Link : 'span'"
                        v-for="link in announcements.links"
                        :key="link.label"
                        :href="link.url"
                        class="inline-flex h-9 items-center rounded-lg border px-3 text-sm"
                        :class="link.active ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-200 bg-white text-slate-700'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
