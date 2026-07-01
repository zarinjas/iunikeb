<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, BarChart3, Megaphone, Pencil, Pin, Send, Trash2 } from 'lucide-vue-next';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    announcement: { type: Object, required: true },
    canPublish: { type: Boolean, default: false },
    canEdit: { type: Boolean, default: false },
    canDelete: { type: Boolean, default: false },
});

const handlePublish = () => {
    if (confirm('Terbitkan pengumuman ini?')) {
        router.post(`/admin/announcements/${props.announcement.id}/publish`, {}, { preserveScroll: true });
    }
};

const handleUnpublish = () => {
    if (confirm('Nyahterbitkan pengumuman ini?')) {
        router.post(`/admin/announcements/${props.announcement.id}/unpublish`, {}, { preserveScroll: true });
    }
};

const handleDelete = () => {
    if (confirm('Padam pengumuman ini? Tindakan ini tidak boleh dibatalkan.')) {
        router.delete(`/admin/announcements/${props.announcement.id}`, {
            onFinish: () => window.location.href = '/admin/announcements',
        });
    }
};

const priorityColor = (priority) => {
    switch (priority) {
        case 'penting': return 'border-amber-200 bg-amber-50 text-amber-700';
        case 'segera': return 'border-red-200 bg-red-50 text-red-700';
        default: return 'border-teal-200 bg-teal-50 text-teal-700';
    }
};
</script>

<template>
    <Head :title="announcement.title" />

    <AdminLayout>
        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <Button :as="Link" href="/admin/announcements" variant="outline" size="sm">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Kembali
                </Button>
                <div class="flex items-center gap-2">
                    <Button
                        v-if="canPublish && announcement.status === 'draft'"
                        type="button"
                        size="sm"
                        @click="handlePublish"
                    >
                        <Send class="mr-2 h-4 w-4" />
                        Terbitkan
                    </Button>
                    <Button
                        v-if="canPublish && announcement.status === 'published'"
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="handleUnpublish"
                    >
                        Nyahterbitkan
                    </Button>
                    <Button
                        v-if="canEdit"
                        :as="Link"
                        :href="`/admin/announcements/${announcement.id}/edit`"
                        variant="outline"
                        size="sm"
                    >
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit
                    </Button>
                    <Button
                        v-if="announcement.targeted_count > 0"
                        :as="Link"
                        :href="`/admin/announcements/${announcement.id}/read-stats`"
                        variant="outline"
                        size="sm"
                    >
                        <BarChart3 class="mr-2 h-4 w-4" />
                        Jejak Bacaan
                    </Button>
                    <Button
                        v-if="canDelete"
                        type="button"
                        variant="destructive"
                        size="sm"
                        @click="handleDelete"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Padam
                    </Button>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-wrap items-center gap-3">
                    <StatusBadge :status="announcement.status" />
                    <span :class="['inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold', priorityColor(announcement.priority)]">
                        {{ announcement.priority_label }}
                    </span>
                    <StatusBadge :status="`${announcement.audience}`" :label="announcement.audience_label" />
                    <span v-if="announcement.is_pinned" class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                        <Pin class="h-3 w-3" />
                        Dipin
                    </span>
                    <span class="text-xs text-slate-400">
                        Dicipta oleh {{ announcement.created_by_name || '-' }} &middot; {{ announcement.created_at }}
                    </span>
                </div>

                <h1 class="mb-2 text-xl font-semibold text-slate-950">{{ announcement.title }}</h1>

                <p v-if="announcement.summary" class="mb-6 text-sm font-medium text-slate-600">{{ announcement.summary }}</p>

                <div v-if="announcement.image_url" class="mb-6 overflow-hidden rounded-2xl">
                    <img :src="announcement.image_url" :alt="announcement.title" class="w-full object-cover">
                </div>

                <div v-if="announcement.content" class="space-y-4 text-sm leading-7 text-slate-700">
                    <p v-for="(p, i) in (announcement.content || '').split('\n').filter(Boolean)" :key="i">{{ p }}</p>
                </div>

                <div class="mt-6 flex flex-wrap gap-4 border-t border-slate-100 pt-4 text-xs text-slate-400">
                    <span v-if="announcement.published_at">Diterbitkan: {{ announcement.published_at }}</span>
                    <span v-if="announcement.expires_at">Tamat: {{ announcement.expires_at }}</span>
                    <span>Saluran: {{ announcement.send_via === 'in_app,email' ? 'In-App + E-mel' : 'In-App sahaja' }}</span>
                    <span v-if="announcement.targeted_count > 0">
                        <BarChart3 class="mr-1 inline h-3 w-3" />
                        {{ announcement.read_count }} / {{ announcement.targeted_count }} dibaca
                    </span>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>
