<script setup>
import { CheckCircle, Download, Loader2, Send, Upload } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/Shared/Components/ui/button';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';

const props = defineProps({
    documents: { type: Array, default: () => [] },
    canSubmit: { type: Boolean, default: false },
    submitUrl: { type: String, default: '' },
});

const files = ref({});
const uploading = ref({});
const submitLoading = ref(false);

const requiredDocs = computed(() => props.documents.filter((d) => d.requires_upload));
const uploadedDocs = computed(() => requiredDocs.value.filter((d) => d.status === 'uploaded'));
const allRequiredUploaded = computed(() => requiredDocs.value.length > 0 && uploadedDocs.value.length === requiredDocs.value.length);

const upload = (document) => {
    const file = files.value[document.id];
    if (!file) return;

    uploading.value[document.id] = true;
    const fd = new FormData();
    fd.append('file', file, file.name);

    router.post(document.upload_url, fd, {
        onFinish: () => {
            uploading.value[document.id] = false;
            files.value[document.id] = null;
        },
    });
};

const submitAll = () => {
    if (!props.canSubmit || !props.submitUrl) return;
    submitLoading.value = true;
    router.post(props.submitUrl, {}, {
        onFinish: () => { submitLoading.value = false; },
    });
};
</script>

<template>
    <div v-if="documents.length" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-slate-950">Pakej Dokumen</h2>
            <p class="mt-1 text-sm text-slate-500">Muat turun dokumen, lengkapkan tandatangan atau cop jika perlu, kemudian muat naik semula.</p>
        </div>

        <div class="space-y-3">
            <div v-for="document in documents" :key="document.id" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-950">{{ document.name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ document.code }}</p>
                        <p v-if="document.uploaded_file_name" class="mt-1 text-xs text-slate-600">
                            Dimuat naik: {{ document.uploaded_file_name }}
                        </p>
                        <p v-if="document.rejection_reason" class="mt-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                            {{ document.rejection_reason }}
                        </p>
                    </div>
                    <StatusBadge :status="document.status" :label="document.status.replaceAll('_', ' ')" />
                </div>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <Button v-if="document.generated" :as="'a'" :href="document.download_url" type="button" variant="outline" size="sm">
                        <Download class="mr-2 h-4 w-4" />
                        Muat Turun
                    </Button>

                    <template v-if="document.requires_upload">
                        <input
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-teal-700 hover:file:bg-teal-100"
                            @change="(event) => files[document.id] = event.target.files?.[0] ?? null"
                        />
                        <Button type="button" size="sm" :disabled="!files[document.id] || uploading[document.id]" @click="upload(document)">
                            <Upload class="mr-2 h-4 w-4" />
                            {{ uploading[document.id] ? 'Memuat Naik...' : 'Muat Naik' }}
                        </Button>
                    </template>
                </div>
            </div>
        </div>

        <div v-if="requiredDocs.length > 0" class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center gap-3">
                <CheckCircle v-if="allRequiredUploaded" class="h-5 w-5 shrink-0 text-teal-600" />
                <Upload v-else class="h-5 w-5 shrink-0 text-slate-400" />
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900">
                        {{ uploadedDocs.length }}/{{ requiredDocs.length }} dokumen dimuat naik
                    </p>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                        <div
                            class="h-full rounded-full transition-all duration-300"
                            :class="allRequiredUploaded ? 'bg-teal-500' : 'bg-teal-400'"
                            :style="{ width: ((uploadedDocs.length / requiredDocs.length) * 100) + '%' }"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="canSubmit && allRequiredUploaded" class="mt-4">
            <Button type="button" class="w-full" :disabled="submitLoading" @click="submitAll">
                <Loader2 v-if="submitLoading" class="mr-2 h-4 w-4 animate-spin" />
                <Send v-else class="mr-2 h-4 w-4" />
                {{ submitLoading ? 'Menghantar...' : 'Selesai dan Hantar' }}
            </Button>
        </div>
    </div>
</template>
