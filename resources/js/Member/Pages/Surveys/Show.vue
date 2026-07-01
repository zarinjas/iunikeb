<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Vote, CheckCircle2, ChevronLeft, Clock } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import MemberLayout from '@/Member/Layouts/MemberLayout.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    survey: { type: Object, required: true },
    options: { type: Array, required: true },
    hasVoted: { type: Boolean, default: false },
});

const form = useForm({
    survey_option_id: '',
});

const submitVote = () => {
    form.post(`/member/surveys/${props.survey.id}/vote`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const selectedLabel = computed(() => {
    const opt = props.options.find(o => o.id === form.survey_option_id);
    return opt ? opt.label : '';
});
</script>

<template>
    <Head :title="survey.question" />
    <MemberLayout>
        <div class="space-y-4 pb-28 max-w-xl mx-auto">
            <Link
                href="/member/surveys"
                class="inline-flex items-center gap-1 text-xs font-medium text-teal-600 hover:text-teal-700"
            >
                <ChevronLeft class="h-3.5 w-3.5" />
                Kembali ke senarai
            </Link>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-500">
                        <Vote class="h-5 w-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-base font-bold text-slate-900">{{ survey.question }}</h1>
                        <p v-if="survey.description" class="mt-1 text-xs leading-5 text-slate-500">{{ survey.description }}</p>
                        <div class="mt-2 flex items-center gap-3 text-[11px] text-slate-400">
                            <span class="flex items-center gap-1">
                                <Clock class="h-3 w-3" />
                                {{ survey.expires_at ? 'Tamat ' + survey.expires_at : 'Tiada tarikh tamat' }}
                            </span>
                            <span>{{ survey.total_responses }} ahli telah mengundi</span>
                        </div>

                        <!-- Already voted message -->
                        <div
                            v-if="hasVoted"
                            class="mt-4 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
                        >
                            <CheckCircle2 class="h-5 w-5 shrink-0" />
                            Terima kasih! Undian anda telah direkodkan.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vote Form -->
            <div v-if="!hasVoted" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Pilih jawapan anda</h2>

                <div class="space-y-2">
                    <label
                        v-for="option in options"
                        :key="option.id"
                        class="flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition"
                        :class="form.survey_option_id === option.id ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-100' : 'border-slate-200 hover:border-slate-300'"
                    >
                        <input
                            v-model="form.survey_option_id"
                            type="radio"
                            name="survey_option"
                            :value="option.id"
                            class="h-4 w-4 border-slate-300 text-violet-600 focus:ring-violet-200"
                        />
                        <span class="text-sm font-medium text-slate-700">{{ option.label }}</span>
                    </label>
                </div>

                <p v-if="form.errors.survey_option_id" class="mt-2 text-sm font-medium text-red-500">{{ form.errors.survey_option_id }}</p>

                <Button type="button" class="mt-4 w-full" :disabled="!form.survey_option_id || form.processing" @click="submitVote">
                    {{ form.processing ? 'Menghantar...' : 'Hantar Undian' }}
                </Button>
            </div>
        </div>
    </MemberLayout>
</template>
