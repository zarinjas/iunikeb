<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Vote, ChevronRight, CheckCircle2, Clock } from 'lucide-vue-next';
import MemberLayout from '@/Member/Layouts/MemberLayout.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';

const props = defineProps({
    surveys: { type: Object, required: true },
});
</script>

<template>
    <Head title="Undian" />
    <MemberLayout>
        <div class="space-y-4 pb-28">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-sm">
                    <Vote class="h-5 w-5" />
                </span>
                <div>
                    <h1 class="text-lg font-bold text-slate-900">Undian</h1>
                    <p class="text-xs text-slate-400">Sertai undian dan kongsikan pendapat anda</p>
                </div>
            </div>

            <EmptyState
                v-if="surveys.data.length === 0"
                title="Tiada undian aktif"
                description="Belum ada undian yang diterbitkan buat masa ini."
                compact
            />

            <div v-else class="space-y-3">
                <Link
                    v-for="survey in surveys.data"
                    :key="survey.id"
                    :href="`/member/surveys/${survey.id}`"
                    class="block rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                            :class="survey.has_voted ? 'bg-emerald-50 text-emerald-500' : 'bg-violet-50 text-violet-500'"
                        >
                            <component :is="survey.has_voted ? CheckCircle2 : Vote" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900">{{ survey.question }}</p>
                            <p v-if="survey.description" class="mt-0.5 line-clamp-1 text-xs text-slate-400">{{ survey.description }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-400">
                                <span class="flex items-center gap-1">
                                    <Clock class="h-3 w-3" />
                                    {{ survey.expires_at ? 'Tamat ' + survey.expires_at : 'Tiada tarikh tamat' }}
                                </span>
                                <span>{{ survey.total_responses }} undian</span>
                                <span
                                    v-if="survey.has_voted"
                                    class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-600"
                                >
                                    Selesai
                                </span>
                            </div>
                        </div>
                        <ChevronRight class="mt-1 h-4 w-4 shrink-0 text-slate-300" />
                    </div>
                </Link>
            </div>

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
        </div>
    </MemberLayout>
</template>
