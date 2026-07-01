<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Vote, Search, X, Eye, Pencil, Trash2, Send, Ban } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import ConfirmDialog from '@/Shared/Components/ConfirmDialog.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import SearchInput from '@/Shared/Components/SearchInput.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    surveys: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const statusLabels = { draft: 'Draf', published: 'Diterbitkan', closed: 'Ditutup' };
const statusColors = { draft: 'draft', published: 'success', closed: 'inactive' };

const deleteTarget = ref(null);
const publishTarget = ref(null);
const closeTarget = ref(null);

const activeStatus = computed(() => props.filters.status || '');

const setStatusFilter = (status) => {
    router.get('/admin/surveys', { status: status || undefined }, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <Head title="Undian" />
    <AdminLayout>
        <section class="space-y-6">
            <PageHeader title="Undian" description="Urus undian dan kaji selidik untuk ahli.">
                <template #actions>
                    <Button :as="Link" href="/admin/surveys/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Cipta Undian
                    </Button>
                </template>
            </PageHeader>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    class="rounded-full px-3.5 py-1.5 text-xs font-medium transition"
                    :class="activeStatus === '' ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    @click="setStatusFilter('')"
                >
                    Semua
                </button>
                <button
                    v-for="(label, key) in statusLabels"
                    :key="key"
                    type="button"
                    class="rounded-full px-3.5 py-1.5 text-xs font-medium transition"
                    :class="activeStatus === key ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    @click="setStatusFilter(key)"
                >
                    {{ label }}
                </button>
                <button
                    v-if="activeStatus"
                    type="button"
                    class="ml-2 flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1.5 text-xs text-slate-500 transition hover:bg-slate-200"
                    @click="setStatusFilter('')"
                >
                    <X class="h-3 w-3" />
                    Kosongkan
                </button>
            </div>

            <EmptyState
                v-if="surveys.data.length === 0"
                title="Tiada undian"
                description="Undian yang dicipta akan dipaparkan di sini."
                compact
            />

            <div v-else class="space-y-2">
                <div
                    v-for="survey in surveys.data"
                    :key="survey.id"
                    class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm transition hover:shadow-md"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-500">
                        <Vote class="h-5 w-5" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900">{{ survey.question }}</p>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-slate-400">
                            <StatusBadge :status="survey.status" :label="statusLabels[survey.status] || survey.status" />
                            <span>{{ survey.options_count }} pilihan</span>
                            <span>{{ survey.responses_count }} undian</span>
                            <span v-if="survey.expires_at">Tamat: {{ survey.expires_at }}</span>
                            <span>{{ survey.created_at }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Link
                            :href="`/admin/surveys/${survey.id}`"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                            title="Lihat Keputusan"
                        >
                            <Eye class="h-4 w-4" />
                        </Link>
                        <Link
                            v-if="survey.status === 'draft'"
                            :href="`/admin/surveys/${survey.id}/edit`"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                            title="Edit"
                        >
                            <Pencil class="h-4 w-4" />
                        </Link>
                        <button
                            v-if="survey.status === 'draft'"
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-600"
                            title="Terbitkan"
                            @click="publishTarget = survey.id"
                        >
                            <Send class="h-4 w-4" />
                        </button>
                        <button
                            v-if="survey.status === 'published'"
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-amber-50 hover:text-amber-600"
                            title="Tutup Undian"
                            @click="closeTarget = survey.id"
                        >
                            <Ban class="h-4 w-4" />
                        </button>
                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                            title="Padam"
                            @click="deleteTarget = survey.id"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="surveys.last_page > 1" class="flex items-center justify-between">
                <p class="text-xs text-slate-400">Halaman {{ surveys.current_page }} dari {{ surveys.last_page }}</p>
                <div class="flex gap-2">
                    <Link
                        v-if="surveys.prev_page_url"
                        :href="surveys.prev_page_url"
                        class="rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-slate-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50"
                    >
                        Sebelum
                    </Link>
                    <Link
                        v-if="surveys.next_page_url"
                        :href="surveys.next_page_url"
                        class="rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-slate-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50"
                    >
                        Seterus
                    </Link>
                </div>
            </div>
        </section>

        <ConfirmDialog
            :open="Boolean(deleteTarget)"
            title="Padam undian"
            description="Undian ini akan dipadam secara kekal. Tindakan ini tidak boleh dikembalikan."
            confirm-label="Padam"
            @cancel="deleteTarget = null"
            @confirm="router.post(`/admin/surveys/${deleteTarget}`, { _method: 'DELETE' }, { preserveScroll: true, onFinish: () => { deleteTarget = null; } })"
        />

        <ConfirmDialog
            :open="Boolean(publishTarget)"
            title="Terbitkan undian"
            description="Undian akan boleh diakses oleh ahli sebaik sahaja diterbitkan."
            confirm-label="Terbitkan"
            @cancel="publishTarget = null"
            @confirm="router.post(`/admin/surveys/${publishTarget}/publish`, {}, { preserveScroll: true, onFinish: () => { publishTarget = null; } })"
        />

        <ConfirmDialog
            :open="Boolean(closeTarget)"
            title="Tutup undian"
            description="Ahli tidak lagi boleh menghantar undian selepas ini ditutup."
            confirm-label="Tutup"
            @cancel="closeTarget = null"
            @confirm="router.post(`/admin/surveys/${closeTarget}/close`, {}, { preserveScroll: true, onFinish: () => { closeTarget = null; } })"
        />
    </AdminLayout>
</template>
