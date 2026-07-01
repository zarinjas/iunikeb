<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Pencil, Pin, PinOff, Trash2 } from 'lucide-vue-next';
import { computed, reactive } from 'vue';
import AdminFilterBar from '@/Admin/Components/AdminFilterBar.vue';
import AdminRowActions from '@/Shared/Components/AdminRowActions.vue';
import AdminSearchInput from '@/Admin/Components/AdminSearchInput.vue';
import AdminSelectFilter from '@/Admin/Components/AdminSelectFilter.vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import DataTable from '@/Shared/Components/DataTable.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    filters: { type: Object, required: true },
    announcements: { type: Object, required: true },
    statusOptions: { type: Array, required: true },
    audienceOptions: { type: Array, required: true },
    priorityOptions: { type: Array, required: true },
    canCreate: { type: Boolean, default: false },
    canPublish: { type: Boolean, default: false },
    canDelete: { type: Boolean, default: false },
});

const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    audience: props.filters.audience || '',
    priority: props.filters.priority || '',
});

const columns = [
    { key: 'title', label: 'Tajuk' },
    { key: 'priority', label: 'Keutamaan' },
    { key: 'audience', label: 'Audiens' },
    { key: 'status', label: 'Status' },
    { key: 'pinned', label: 'Dipin' },
    { key: 'targeted', label: 'Dibaca' },
    { key: 'actions', label: 'Tindakan' },
];

const getActions = (row) => {
    const actions = [];
    actions.push({ label: 'Lihat', icon: Eye, href: row.show_url });
    if (props.canPublish) {
        actions.push({ label: 'Edit', icon: Pencil, href: row.edit_url });
    }
    return actions;
};

const applyFilters = () => {
    router.get('/admin/announcements', filters, { preserveState: true, replace: true });
};

const resetFilters = () => {
    filters.search = '';
    filters.status = '';
    filters.audience = '';
    filters.priority = '';
    applyFilters();
};

const togglePin = (row) => {
    router.post(`/admin/announcements/${row.id}/toggle-pin`, {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="Pengumuman" />

    <AdminLayout>
        <section class="space-y-6">
            <PageHeader
                title="Pengumuman"
                description="Urus pengumuman, penerbitan, dan jejak bacaan ahli."
            >
                <template #actions>
                    <Button v-if="canCreate" :as="Link" href="/admin/announcements/create">
                        + Pengumuman Baharu
                    </Button>
                </template>
            </PageHeader>

            <AdminFilterBar>
                <AdminSearchInput id="announcement-search" v-model="filters.search" placeholder="Cari tajuk atau ringkasan" />
                <AdminSelectFilter id="announcement-status" v-model="filters.status" label="Status" :options="statusOptions" />
                <AdminSelectFilter id="announcement-audience" v-model="filters.audience" label="Audiens" :options="audienceOptions" />
                <AdminSelectFilter id="announcement-priority" v-model="filters.priority" label="Keutamaan" :options="priorityOptions" />
                <template #actions>
                    <Button type="button" variant="outline" class="h-11" @click="resetFilters">Set Semula</Button>
                    <Button type="button" class="h-11" @click="applyFilters">Tapis</Button>
                </template>
            </AdminFilterBar>

            <EmptyState
                v-if="announcements.data.length === 0"
                title="Tiada pengumuman."
                description="Klik 'Pengumuman Baharu' untuk mencipta pengumuman pertama."
            />

            <DataTable v-else :columns="columns" :rows="announcements.data">
                <template #cell-title="{ row }">
                    <div class="space-y-1">
                        <p class="font-semibold text-slate-950">{{ row.title }}</p>
                        <p class="text-xs text-slate-500">{{ row.created_by_name || '-' }} &middot; {{ row.published_at || '-' }}</p>
                    </div>
                </template>

                <template #cell-priority="{ row }">
                    <StatusBadge :status="row.priority" />
                </template>

                <template #cell-audience="{ row }">
                    <StatusBadge :status="`${row.audience}`" :label="row.audience_label" />
                </template>

                <template #cell-status="{ row }">
                    <StatusBadge :status="row.status" />
                </template>

                <template #cell-pinned="{ row }">
                    <button
                        v-if="canPublish"
                        class="text-slate-400 hover:text-amber-500 transition-colors"
                        @click="togglePin(row)"
                    >
                        <Pin v-if="row.is_pinned" class="h-4 w-4 text-amber-500" />
                        <PinOff v-else class="h-4 w-4" />
                    </button>
                    <Pin v-else-if="row.is_pinned" class="h-4 w-4 text-amber-500" />
                    <span v-else class="text-slate-300">-</span>
                </template>

                <template #cell-targeted="{ row }">
                    <span class="text-xs text-slate-500">{{ row.read_count }} / {{ row.targeted_count || '-' }}</span>
                </template>

                <template #cell-actions="{ row }">
                    <AdminRowActions :actions="getActions(row)" />
                </template>
            </DataTable>

            <div v-if="announcements.links?.length > 3" class="flex flex-wrap gap-2">
                <Button
                    v-for="link in announcements.links"
                    :key="`${link.label}-${link.url}`"
                    :as="link.url ? Link : 'button'"
                    :href="link.url || undefined"
                    :variant="link.active ? 'default' : 'outline'"
                    :disabled="!link.url"
                    v-html="link.label"
                />
            </div>
        </section>
    </AdminLayout>
</template>
