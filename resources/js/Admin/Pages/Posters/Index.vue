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
    posters: { type: Object, required: true },
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

    const items = [...props.posters.data];
    const [moved] = items.splice(from, 1);
    items.splice(idx, 0, moved);

    router.post('/admin/posters/reorder', {
        ordered_ids: items.map(i => i.id),
    }, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <Head title="Poster" />
    <AdminLayout>
        <section class="space-y-6">
            <PageHeader title="Poster" description="Urus poster infografik untuk galeri ahli. Seret untuk susun semula.">
                <template #actions>
                    <Button :as="Link" href="/admin/posters/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Poster
                    </Button>
                </template>
            </PageHeader>

            <EmptyState
                v-if="posters.data.length === 0"
                title="Tiada poster"
                description="Poster akan dipaparkan dalam galeri di dashboard ahli."
                compact
            />

            <div v-else class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                <div
                    v-for="(poster, idx) in posters.data"
                    :key="poster.id"
                    draggable="true"
                    class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-all"
                    :class="{
                        'opacity-40 scale-95': dragIndex === idx,
                        'border-teal-400 ring-2 ring-teal-100': dragOverIndex === idx,
                    }"
                    @dragstart="onDragStart($event, idx)"
                    @dragover="onDragOver($event, idx)"
                    @dragend="onDragEnd"
                    @drop="onDrop($event, idx)"
                >
                    <span class="absolute left-1.5 top-1.5 z-10 cursor-grab rounded-md bg-white/80 p-0.5 text-slate-400 shadow-sm backdrop-blur-sm transition hover:text-slate-600 active:cursor-grabbing">
                        <GripVertical class="h-4 w-4" />
                    </span>

                    <div class="aspect-[4/5] overflow-hidden">
                        <img
                            v-if="poster.image_url"
                            :src="poster.image_url"
                            alt="Poster"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center bg-slate-100 text-xs text-slate-400">
                            Tiada
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-1.5 p-2">
                        <div class="flex items-center gap-1.5">
                            <span
                                class="rounded-full px-1.5 py-0.5 text-[10px] font-medium"
                                :class="poster.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                            >
                                {{ poster.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                            <Link2 v-if="poster.link_url" class="h-3 w-3 text-slate-400" />
                        </div>
                        <div class="flex shrink-0 items-center gap-0.5 opacity-0 transition group-hover:opacity-100">
                            <button
                                type="button"
                                class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                :title="poster.is_active ? 'Nyahaktifkan' : 'Aktifkan'"
                                @click="router.post(`/admin/posters/${poster.id}/toggle`, {}, { preserveScroll: true, preserveState: true })"
                            >
                                <ToggleRight v-if="poster.is_active" class="h-3.5 w-3.5 text-emerald-500" />
                                <ToggleLeft v-else class="h-3.5 w-3.5" />
                            </button>
                            <Link
                                :href="`/admin/posters/${poster.id}/edit`"
                                class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                            >
                                <Pencil class="h-3.5 w-3.5" />
                            </Link>
                            <button
                                type="button"
                                class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                                @click="deleteTarget = poster.id"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <ConfirmDialog
            :open="Boolean(deleteTarget)"
            title="Padam poster"
            description="Poster ini akan dipadam secara kekal. Tindakan ini tidak boleh dikembalikan."
            confirm-label="Padam"
            @cancel="deleteTarget = null"
            @confirm="router.post(`/admin/posters/${deleteTarget}`, { _method: 'DELETE' }, { preserveScroll: true, onFinish: () => { deleteTarget = null; } })"
        />
    </AdminLayout>
</template>
