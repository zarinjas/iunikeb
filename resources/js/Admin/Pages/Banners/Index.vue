<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { GripVertical, Link2, Pencil, Plus, Trash2, ToggleLeft, ToggleRight } from 'lucide-vue-next';
import { ref } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import ConfirmDialog from '@/Shared/Components/ConfirmDialog.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    banners: { type: Object, required: true },
});

const deleteTarget = ref(null);
const dragIndex = ref(null);
const dragOverIndex = ref(null);

const onDragStart = (e, idx) => {
    dragIndex.value = idx;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', idx);
};

const onDragOver = (e, idx) => {
    e.preventDefault();
    dragOverIndex.value = idx;
};

const onDragEnd = () => {
    dragIndex.value = null;
    dragOverIndex.value = null;
};

const onDrop = (e, idx) => {
    e.preventDefault();
    const from = dragIndex.value;
    dragIndex.value = null;
    dragOverIndex.value = null;

    if (from === null || from === idx) return;

    const items = [...props.banners.data];
    const [moved] = items.splice(from, 1);
    items.splice(idx, 0, moved);

    router.post('/admin/banners/reorder', {
        ordered_ids: items.map(i => i.id),
    }, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <Head title="Banner" />
    <AdminLayout>
        <section class="space-y-6">
            <PageHeader title="Banner" description="Urus banner carousel untuk dashboard ahli. Seret untuk susun semula.">
                <template #actions>
                    <Button :as="Link" href="/admin/banners/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Banner
                    </Button>
                </template>
            </PageHeader>

            <EmptyState
                v-if="banners.data.length === 0"
                title="Tiada banner"
                description="Banner akan dipaparkan dalam carousel di dashboard ahli."
                compact
            />

            <div v-else class="space-y-2">
                <div
                    v-for="(banner, idx) in banners.data"
                    :key="banner.id"
                    draggable="true"
                    class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-3 shadow-sm transition-all"
                    :class="{
                        'opacity-40': dragIndex === idx,
                        'border-teal-400 ring-2 ring-teal-100': dragOverIndex === idx,
                    }"
                    @dragstart="onDragStart($event, idx)"
                    @dragover="onDragOver($event, idx)"
                    @dragend="onDragEnd"
                    @drop="onDrop($event, idx)"
                >
                    <span class="cursor-grab text-slate-300 transition hover:text-slate-500 active:cursor-grabbing">
                        <GripVertical class="h-5 w-5" />
                    </span>

                    <div class="h-16 w-28 shrink-0 overflow-hidden rounded-lg ring-1 ring-slate-200">
                        <img
                            v-if="banner.image_url"
                            :src="banner.image_url"
                            alt="Banner"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center bg-slate-100 text-xs text-slate-400">
                            Tiada
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span
                                class="rounded-full px-2 py-0.5 text-[10px] font-medium"
                                :class="banner.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                            >
                                {{ banner.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                            <span v-if="banner.link_url" class="inline-flex items-center gap-0.5 text-[10px] text-slate-400">
                                <Link2 class="h-3 w-3" />
                                Pautan
                            </span>
                        </div>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ banner.created_at }}</p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1 opacity-0 transition group-hover:opacity-100">
                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                            :title="banner.is_active ? 'Nyahaktifkan' : 'Aktifkan'"
                            @click="router.post(`/admin/banners/${banner.id}/toggle`, {}, { preserveScroll: true, preserveState: true })"
                        >
                            <ToggleRight v-if="banner.is_active" class="h-4 w-4 text-emerald-500" />
                            <ToggleLeft v-else class="h-4 w-4" />
                        </button>
                        <Link
                            :href="`/admin/banners/${banner.id}/edit`"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        >
                            <Pencil class="h-4 w-4" />
                        </Link>
                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                            @click="deleteTarget = banner.id"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <ConfirmDialog
            :open="Boolean(deleteTarget)"
            title="Padam banner"
            description="Banner ini akan dipadam secara kekal. Tindakan ini tidak boleh dikembalikan."
            confirm-label="Padam"
            @cancel="deleteTarget = null"
            @confirm="router.post(`/admin/banners/${deleteTarget}`, { _method: 'DELETE' }, { preserveScroll: true, onFinish: () => { deleteTarget = null; } })"
        />
    </AdminLayout>
</template>
