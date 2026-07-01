<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Share2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import BorangFieldRenderer from '@/Shared/Components/BorangFieldRenderer.vue';
import FormDocumentHeader from '@/Shared/Components/FormDocumentHeader.vue';
import FormSection from '@/Shared/Components/FormSection.vue';
import MemberLayout from '@/Member/Layouts/MemberLayout.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import { Button } from '@/Shared/Components/ui/button';
import { useAutofill } from '@/Shared/Composables/useAutofill';
import { getFieldTypeConfig, MEMBER_FIELD_MAP } from '@/Admin/Helpers/formFieldTypes';

const props = defineProps({
    formRecord: { type: Object, required: true },
    autofillData: { type: Object, default: () => ({}) },
});

const { tryFill, isAutofilled, autofillData } = useAutofill(props);

const form = useForm({
    answers: {},
    files: {},
});

for (const section of props.formRecord.sections) {
    for (const field of section.fields) {
        if (!['online_and_print', 'online_only'].includes(field.display_mode)) {
            continue;
        }

        if (field.type === 'checkbox') {
            form.answers[field.field_key] = [];
        } else if (field.type === 'address_my' || field.type === 'member_address') {
            form.answers[field.field_key] = { line1: '', line2: '', postcode: '', city: '', state: '' };
        } else if (field.type === 'agreement_checkbox') {
            form.answers[field.field_key] = false;
        } else {
            form.answers[field.field_key] = '';
        }
        form.files[field.field_key] = null;
    }
}

function composeAddressFromAutofill() {
    const ad = props.autofillData;
    return {
        line1: ad.address_line_1 || ad.address || '',
        line2: ad.address_line_2 || '',
        postcode: ad.postcode || '',
        city: ad.city || '',
        state: ad.state || '',
    };
}

onMounted(() => {
    for (const section of props.formRecord.sections) {
        for (const field of section.fields) {
            if (!['online_and_print', 'online_only'].includes(field.display_mode)) {
                continue;
            }

            if (field.type === 'file' || field.type === 'signature' || field.type === 'agreement_checkbox'
                || field.type === 'note' || field.type === 'instruction_text' || field.type === 'office_use_box') {
                continue;
            }

            if (field.type === 'member_address') {
                const addr = composeAddressFromAutofill();
                if (addr.line1 || addr.postcode) {
                    form.answers[field.field_key] = addr;
                }
                continue;
            }

            const filled = tryFill(form.answers, field.field_key);

            if (!filled) {
                const config = getFieldTypeConfig(field.type);
                if (config?.isMemberAutofill) {
                    const mappedKey = MEMBER_FIELD_MAP[field.type];
                    if (mappedKey && autofillData[mappedKey]) {
                        tryFill(form.answers, field.field_key, () => autofillData[mappedKey]);
                    }
                }
            }
        }
    }
});

const isInputVisible = (field) => ['online_and_print', 'online_only'].includes(field.display_mode);

const submit = () => {
    form.post(`/member/forms/${props.formRecord.slug}`, {
        forceFormData: true,
        onSuccess: () => window.scrollTo({ top: 0, behavior: 'smooth' }),
        onError: () => window.scrollTo({ top: 0, behavior: 'smooth' }),
    });
};

const copied = ref(false);

const shareForm = async () => {
    const url = window.location.href;
    if (navigator.share) {
        try {
            await navigator.share({ title: props.formRecord.title, url });
        } catch {}
    } else {
        try {
            await navigator.clipboard.writeText(url);
            copied.value = true;
            setTimeout(() => { copied.value = false; }, 3000);
        } catch {}
    }
};

const answerError = (fieldKey) => form.errors[`answers.${fieldKey}`] || null;
const fileError = (fieldKey) => form.errors[`files.${fieldKey}`] || null;
</script>

<template>
    <Head :title="formRecord.title" />

    <MemberLayout>
        <section class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <StatusBadge status="published" label="Ahli sahaja" />
                        <span class="text-sm text-slate-500">{{ formRecord.category_name || 'Borang Online' }}</span>
                    </div>
                    <h1 class="mt-2 text-2xl font-semibold text-slate-950">{{ formRecord.title }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <Button variant="outline" @click="shareForm">
                            <Share2 class="mr-2 h-4 w-4" />
                            Kongsi
                        </Button>
                        <Transition name="fade">
                            <div v-if="copied" class="absolute right-0 top-full mt-2 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg">
                                Link borang telah disalin
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>

            <FormDocumentHeader v-if="formRecord.show_document_header" :form-record="formRecord" />

            <div v-if="!formRecord.show_document_header && formRecord.description" class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm leading-6 text-slate-600">{{ formRecord.description }}</p>
            </div>

            <div v-if="formRecord.submission_method === 'requires_stamped_upload'" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                <p class="font-semibold">Perhatian: Borang ini memerlukan cop dan tandatangan</p>
                <p class="mt-1">{{ formRecord.stamped_upload_instructions }}</p>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <FormSection
                    v-for="section in formRecord.sections"
                    :key="section.id"
                    :title="section.title"
                    :description="section.description"
                >
                    <template v-for="field in section.fields" :key="field.id">
                        <div v-if="!isInputVisible(field)" class="hidden" />

                        <BorangFieldRenderer
                            v-else
                            :field="field"
                            :model-value="form.answers[field.field_key]"
                            :file-value="form.files[field.field_key]"
                            :error="field.type === 'file' ? fileError(field.field_key) : answerError(field.field_key)"
                            :autofilled="isAutofilled(field.field_key)"
                            @update:model-value="form.answers[field.field_key] = $event"
                            @update:file-value="form.files[field.field_key] = $event"
                        />
                    </template>
                </FormSection>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">Hantar borang</h2>
                            <p v-if="formRecord.submission_method === 'requires_stamped_upload'" class="text-sm leading-6 text-slate-600">
                                Selepas menghantar, anda perlu mencetak borang yang dilengkapkan, mendapatkan cop dan tandatangan, kemudian muat naik semula borang tersebut.
                            </p>
                            <p v-else class="text-sm leading-6 text-slate-600">Sila semak semula maklumat yang diisi sebelum menghantar borang ini.</p>
                        </div>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Menghantar...' : (formRecord.submission_method === 'requires_stamped_upload' ? 'Teruskan' : 'Hantar Borang') }}
                        </Button>
                    </div>
                </div>
            </form>
        </section>
    </MemberLayout>
</template>
