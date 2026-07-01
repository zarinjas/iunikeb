<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, ChevronLeft, Pin, Zap, Megaphone } from 'lucide-vue-next';
import { computed } from 'vue';
import MemberLayout from '@/Member/Layouts/MemberLayout.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    announcement: { type: Object, required: true },
});

const paragraphs = computed(() => (props.announcement.content || '').split('\n').filter(Boolean));

const priorityBadge = computed(() => {
    switch (props.announcement.priority) {
        case 'penting': return 'border-amber-200 bg-amber-50 text-amber-700';
        case 'segera': return 'border-red-200 bg-red-50 text-red-700';
        default: return 'border-teal-200 bg-teal-50 text-teal-700';
    }
});

const priorityLabel = computed(() => {
    switch (props.announcement.priority) {
        case 'penting': return 'Penting';
        case 'segera': return 'Segera';
        default: return 'Normal';
    }
});
</script>

<template>
    <Head :title="announcement.title" />

    <MemberLayout>
        <div class="space-y-6">
            <Button :as="Link" href="/member/announcements" variant="outline" class="w-fit">
                <ChevronLeft class="mr-2 h-4 w-4" />
                Kembali ke senarai
            </Button>

            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div v-if="announcement.image_url" class="-mx-6 -mt-6 mb-6 overflow-hidden rounded-t-3xl">
                    <img :src="announcement.image_url" :alt="announcement.title" class="w-full object-cover max-h-64">
                </div>

                <div class="mb-6 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-2">
                        <CalendarDays class="h-4 w-4 text-teal-700" />
                        {{ announcement.published_at || '-' }}
                    </span>
                    <span v-if="announcement.is_pinned" class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                        <Pin class="h-3.5 w-3.5" />
                        Dipin
                    </span>
                    <span :class="['inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold', priorityBadge]">
                        {{ priorityLabel }}
                    </span>
                    <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ announcement.audience_label }}
                    </span>
                </div>

                <h1 class="mb-4 text-xl font-semibold text-slate-950">{{ announcement.title }}</h1>

                <div v-if="announcement.summary" class="mb-4 rounded-xl bg-slate-50 p-4 text-sm font-medium text-slate-600">
                    {{ announcement.summary }}
                </div>

                <div class="space-y-4 text-sm leading-7 text-slate-700">
                    <p v-if="paragraphs.length === 0">{{ announcement.summary }}</p>
                    <p v-for="paragraph in paragraphs" :key="paragraph">{{ paragraph }}</p>
                </div>
            </article>
        </div>
    </MemberLayout>
</template>
