<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Vote, Ban, BarChart3, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import ConfirmDialog from '@/Shared/Components/ConfirmDialog.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';
import { Button } from '@/Shared/Components/ui/button';
import { ref } from 'vue';

const props = defineProps({
    survey: { type: Object, required: true },
    options: { type: Array, required: true },
});

const statusLabels = { draft: 'Draf', published: 'Diterbitkan', closed: 'Ditutup' };
const closeTarget = ref(false);

const maxCount = computed(() => Math.max(...props.options.map(o => o.count), 1));

const barColor = (idx) => {
    const colors = ['teal', 'violet', 'amber', 'rose', 'sky', 'emerald', 'orange', 'cyan'];
    return colors[idx % colors.length];
};
</script>

<template>
    <Head :title="survey.question" />
    <AdminLayout>
        <section class="space-y-6 max-w-3xl">
            <PageHeader :title="survey.question" :description="survey.description || 'Keputusan undian'">
                <template #actions>
                    <Button :as="Link" href="/admin/surveys" variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Kembali
                    </Button>
                </template>
            </PageHeader>

            <!-- Summary -->
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                            <BarChart3 class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">Jumlah Undian</p>
                            <p class="text-2xl font-bold text-slate-900">{{ survey.total_responses }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <StatusBadge :status="survey.status" :label="statusLabels[survey.status]" />
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">Status</p>
                            <p class="text-sm font-semibold text-slate-900">{{ statusLabels[survey.status] || survey.status }}</p>
                            <p v-if="survey.expires_at" class="text-[11px] text-slate-400">Tamat: {{ survey.expires_at }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Chart -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-slate-900">Keputusan Undian</h3>

                <div v-if="survey.total_responses === 0" class="py-8">
                    <EmptyState
                        title="Belum ada undian"
                        description="Tiada ahli telah mengundi setakat ini."
                        compact
                    />
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="(option, idx) in options"
                        :key="option.id"
                        class="group"
                    >
                        <div class="mb-1 flex items-center justify-between">
                            <p class="text-sm font-medium text-slate-700">{{ option.label }}</p>
                            <p class="text-xs text-slate-400">
                                {{ option.count }} undian
                                <span class="ml-1 font-semibold text-slate-600">({{ option.percentage }}%)</span>
                            </p>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                            <div
                                class="h-full rounded-full transition-all duration-500"
                                :class="`bg-${barColor(idx)}-500`"
                                :style="{ width: (option.count / maxCount) * 100 + '%', minWidth: option.count > 0 ? '8px' : '0' }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div v-if="survey.status === 'published'" class="flex justify-end">
                <Button type="button" variant="outline" @click="closeTarget = true">
                    <Ban class="mr-2 h-4 w-4" />
                    Tutup Undian
                </Button>
            </div>
        </section>

        <ConfirmDialog
            :open="closeTarget"
            title="Tutup undian"
            description="Ahli tidak lagi boleh menghantar undian selepas ini ditutup."
            confirm-label="Tutup"
            @cancel="closeTarget = false"
            @confirm="router.post(`/admin/surveys/${survey.id}/close`, {}, { preserveScroll: true, onFinish: () => { closeTarget = false; } })"
        />
    </AdminLayout>
</template>
