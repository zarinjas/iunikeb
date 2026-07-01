<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2, GripVertical } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import FormSection from '@/Shared/Components/FormSection.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import TextInput from '@/Shared/Components/Form/TextInput.vue';
import TextareaInput from '@/Shared/Components/Form/TextareaInput.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    survey: { type: Object, default: null },
});

const isEdit = computed(() => Boolean(props.survey));

const form = useForm({
    question: props.survey?.question || '',
    description: props.survey?.description || '',
    expires_at: props.survey?.expires_at || '',
    options: props.survey?.options?.map(o => o.label) || ['', ''],
});

const optionErrors = ref([]);

const addOption = () => {
    form.options.push('');
};

const removeOption = (idx) => {
    if (form.options.length <= 2) return;
    form.options.splice(idx, 1);
};

const moveOption = (from, to) => {
    if (to < 0 || to >= form.options.length) return;
    const [item] = form.options.splice(from, 1);
    form.options.splice(to, 0, item);
};

const submit = () => {
    optionErrors.value = [];

    const filtered = form.options.filter(o => o.trim() !== '');
    if (filtered.length < 2) {
        optionErrors.value = ['Sekurang-kurangnya 2 pilihan diperlukan.'];
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }

    const unique = new Set(filtered);
    if (unique.size !== filtered.length) {
        optionErrors.value = ['Pilihan tidak boleh sama.'];
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }

    form.options = filtered;

    const url = isEdit.value ? `/admin/surveys/${props.survey.id}` : '/admin/surveys';
    const method = isEdit.value ? 'patch' : 'post';

    form[method](url, {
        preserveScroll: true,
        onError: () => window.scrollTo({ top: 0, behavior: 'smooth' }),
    });
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Undian' : 'Cipta Undian'" />
    <AdminLayout>
        <section class="space-y-6 max-w-3xl">
            <PageHeader
                :title="isEdit ? 'Edit Undian' : 'Cipta Undian'"
                :description="isEdit ? 'Kemas kini soalan dan pilihan undian.' : 'Buat undian baru untuk ahli.'"
            >
                <template #actions>
                    <Button :as="Link" href="/admin/surveys" variant="outline">Kembali</Button>
                </template>
            </PageHeader>

            <form @submit.prevent="submit">
                <FormSection title="Maklumat Undian" description="Soalan dan keterangan ringkas undian.">
                    <div class="space-y-4">
                        <TextInput
                            id="survey-question"
                            v-model="form.question"
                            label="Soalan"
                            placeholder="Contoh: Apakah program yang anda minati?"
                            :error="form.errors.question"
                            required
                        />
                        <TextareaInput
                            id="survey-description"
                            v-model="form.description"
                            label="Penerangan (opsional)"
                            placeholder="Penerangan ringkas tentang undian ini..."
                            :error="form.errors.description"
                            :rows="3"
                        />
                        <TextInput
                            id="survey-expires"
                            v-model="form.expires_at"
                            type="datetime-local"
                            label="Tarikh Tamat (opsional)"
                            :error="form.errors.expires_at"
                        />
                    </div>
                </FormSection>

                <FormSection title="Pilihan Undian" description="Tambah pilihan jawapan untuk ahli. Minimum 2 pilihan." class="mt-6">
                    <div class="space-y-2">
                        <div
                            v-for="(option, idx) in form.options"
                            :key="idx"
                            class="flex items-center gap-2"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-xs font-bold text-slate-400">
                                {{ String.fromCharCode(65 + idx) }}
                            </span>
                            <input
                                v-model="form.options[idx]"
                                type="text"
                                class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm outline-none transition focus:border-teal-400 focus:ring-2 focus:ring-teal-100"
                                :placeholder="`Pilihan ${idx + 1}`"
                            />
                            <button
                                v-if="form.options.length > 2"
                                type="button"
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-300 transition hover:bg-red-50 hover:text-red-500"
                                @click="removeOption(idx)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>

                        <p v-if="optionErrors.length" class="text-sm font-medium text-red-500">{{ optionErrors.join(', ') }}</p>
                        <p v-if="form.errors.options" class="text-sm font-medium text-red-500">{{ form.errors.options }}</p>
                        <p v-if="form.errors['options.0']" class="text-sm font-medium text-red-500">{{ form.errors['options.0'] }}</p>

                        <button
                            type="button"
                            class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-slate-300 px-4 py-2 text-xs font-medium text-slate-500 transition hover:border-teal-300 hover:text-teal-600"
                            @click="addOption"
                        >
                            <Plus class="h-3.5 w-3.5" />
                            Tambah Pilihan
                        </button>
                    </div>
                </FormSection>

                <div class="mt-6 flex justify-end gap-3">
                    <Button :as="Link" href="/admin/surveys" variant="outline">Batal</Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Kemas Kini' : 'Simpan') }}
                    </Button>
                </div>
            </form>
        </section>
    </AdminLayout>
</template>
