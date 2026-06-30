<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { reactive, ref } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import AdminRowActions from '@/Shared/Components/AdminRowActions.vue';
import ConfirmDialog from '@/Shared/Components/ConfirmDialog.vue';
import DataTable from '@/Shared/Components/DataTable.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import SearchInput from '@/Shared/Components/SearchInput.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import { Badge } from '@/Shared/Components/ui/badge';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    filters: { type: Object, required: true },
    posters: { type: Object, required: true },
});

const deleteTarget = ref(null);

const filters = reactive({ search: props.filters.search || '', type: props.filters.type || '' });
const applyFilters = () => router.get('/admin/posters', filters, { preserveState: true, replace: true });

const columns = [
    { key: 'image', label: 'Gambar' },
    { key: 'title', label: 'Tajuk' },
    { key: 'type', label: 'Jenis' },
    { key: 'audience', label: 'Audien' },
    { key: 'active', label: 'Status' },
    { key: 'actions', label: 'Tindakan' },
];

const getActions = (row) => [
    { label: 'Edit', icon: Pencil, href: `/admin/posters/${row.id}/edit` },
    { label: 'Padam', icon: Trash2, variant: 'destructive', onClick: () => { deleteTarget.value = row.id; } },
];

const typeLabel = (type) => type === 'banner' ? 'Banner' : 'Poster';

const audienceLabel = (audience) => {
    const map = { public: 'Awam', members: 'Ahli', both: 'Semua' };
    return map[audience] || audience;
};
</script>

<template>
    <Head title="Poster & Banner" />
    <AdminLayout>
        <section class="space-y-6">
            <PageHeader title="Poster & Banner" description="Urus poster dan banner untuk laman web dan portal ahli.">
                <template #actions>
                    <Button :as="Link" href="/admin/posters/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Poster
                    </Button>
                </template>
            </PageHeader>

            <div class="flex max-w-sm gap-3">
                <SearchInput v-model="filters.search" placeholder="Cari poster..." @update:model-value="applyFilters" />
            </div>

            <EmptyState v-if="posters.data.length === 0" title="Tiada poster." description="Poster dan banner yang ditambah akan dipaparkan di sini." compact />

            <DataTable v-else :columns="columns" :rows="posters.data">
                <template #cell-image="{ row }">
                    <img v-if="row.image_url" :src="row.image_url" :alt="row.title" class="h-12 w-20 rounded-lg object-cover ring-1 ring-slate-200" />
                    <div v-else class="flex h-12 w-20 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-400">Tiada</div>
                </template>
                <template #cell-type="{ row }">
                    <Badge variant="outline">{{ typeLabel(row.type) }}</Badge>
                </template>
                <template #cell-audience="{ row }">
                    <span class="text-sm text-slate-600">{{ audienceLabel(row.audience) }}</span>
                </template>
                <template #cell-active="{ row }">
                    <StatusBadge :status="row.is_active ? 'active' : 'inactive'" />
                </template>
                <template #cell-actions="{ row }">
                    <AdminRowActions :actions="getActions(row)" />
                </template>
            </DataTable>
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
