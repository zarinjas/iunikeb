<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ImagePlus, Search, Send, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import FormSection from '@/Shared/Components/FormSection.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import SelectInput from '@/Shared/Components/Form/SelectInput.vue';
import TextInput from '@/Shared/Components/Form/TextInput.vue';
import TextareaInput from '@/Shared/Components/Form/TextareaInput.vue';
import ToggleSwitch from '@/Shared/Components/Form/ToggleSwitch.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    announcement: { type: Object, required: true },
    audienceOptions: { type: Array, required: true },
    priorityOptions: { type: Array, required: true },
    sendViaOptions: { type: Array, required: true },
    memberOptions: { type: Array, required: true },
    canPublish: { type: Boolean, default: false },
});

const form = useForm({
    title: props.announcement.title,
    slug: props.announcement.slug,
    summary: props.announcement.summary || '',
    content: props.announcement.content || '',
    image: null,
    audience: props.announcement.audience,
    priority: props.announcement.priority,
    send_via: props.announcement.send_via,
    is_pinned: props.announcement.is_pinned,
    published_at: props.announcement.published_at || '',
    expires_at: props.announcement.expires_at || '',
    target_user_ids: [...(props.announcement.targeted_user_ids || [])],
});

const imagePreview = ref(props.announcement.image_url || null);
const memberSearch = ref('');
const showMemberPicker = ref(false);

const filteredMembers = computed(() => {
    if (!memberSearch.value) return props.memberOptions;
    const q = memberSearch.value.toLowerCase();
    return props.memberOptions.filter((m) => m.label.toLowerCase().includes(q));
});

const selectedMembers = computed(() => {
    return props.memberOptions.filter((m) => form.target_user_ids.includes(m.value));
});

const toggleMember = (userId) => {
    const idx = form.target_user_ids.indexOf(userId);
    if (idx === -1) {
        form.target_user_ids.push(userId);
    } else {
        form.target_user_ids.splice(idx, 1);
    }
};

const handleImage = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = props.announcement.image_url || null;
};

const submit = () => {
    form.post(`/admin/announcements/${props.announcement.id}`, {
        _method: 'PUT',
    });
};

const publish = () => {
    if (confirm('Terbitkan pengumuman ini? Ahli terpilih akan menerima notifikasi.')) {
        form.post(`/admin/announcements/${props.announcement.id}/publish`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Edit Pengumuman" />

    <AdminLayout>
        <section class="space-y-6">
            <PageHeader title="Edit Pengumuman" description="Kemas kini kandungan dan tetapan pengumuman.">
                <template #actions>
                    <Button v-if="canPublish && announcement.status !== 'published'" type="button" @click="publish">
                        <Send class="mr-2 h-4 w-4" />
                        Terbitkan
                    </Button>
                </template>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <FormSection title="Kandungan Pengumuman">
                        <div class="space-y-4">
                            <TextInput v-model="form.title" label="Tajuk" required :error="form.errors.title" />

                            <TextInput v-model="form.slug" label="Slug (URL)" :error="form.errors.slug">
                                <template #help>Biarkan kosong untuk janakan automatik.</template>
                            </TextInput>

                            <TextareaInput v-model="form.summary" label="Ringkasan" :rows="2" :error="form.errors.summary">
                                <template #help>Paparan pendek untuk senarai dan notifikasi.</template>
                            </TextareaInput>

                            <TextareaInput v-model="form.content" label="Kandungan Penuh" :rows="6" :error="form.errors.content" />
                        </div>
                    </FormSection>

                    <FormSection title="Imej (Pilihan)">
                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 border-dashed border-slate-200 p-6 text-center text-sm text-slate-500 transition-colors hover:border-teal-300 hover:text-teal-600">
                                <ImagePlus class="h-5 w-5" />
                                Pilih Imej
                                <input type="file" accept="image/*" class="hidden" @change="handleImage">
                            </label>
                            <p v-if="form.errors.image" class="text-xs text-red-600">{{ form.errors.image }}</p>
                            <div v-if="imagePreview" class="relative inline-block">
                                <img :src="imagePreview" class="h-32 w-auto rounded-lg object-cover" alt="Pratonton">
                                <button type="button" class="absolute -right-1.5 -top-1.5 rounded-full bg-red-600 p-0.5 text-white" @click="removeImage">
                                    <X class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                    </FormSection>
                </div>

                <div class="space-y-6">
                    <FormSection title="Tetapan Penerbitan">
                        <div class="space-y-4">
                            <SelectInput v-model="form.audience" label="Audiens Sasaran" :options="audienceOptions" required :error="form.errors.audience" />

                            <SelectInput v-model="form.priority" label="Keutamaan" :options="priorityOptions" required :error="form.errors.priority" />

                            <SelectInput v-model="form.send_via" label="Saluran Hantaran" :options="sendViaOptions" required :error="form.errors.send_via">
                                <template #help>E-mel akan dihantar apabila sistem e-mel disediakan.</template>
                            </SelectInput>

                            <ToggleSwitch id="edit-is-pinned" v-model="form.is_pinned" label="Pin pengumuman" description="Pengumuman dipin akan sentiasa di atas senarai." />

                            <TextInput v-model="form.published_at" label="Tarikh Terbit (Pilihan)" type="datetime-local" :error="form.errors.published_at" />

                            <TextInput v-model="form.expires_at" label="Tarikh Tamat (Pilihan)" type="datetime-local" :error="form.errors.expires_at" />
                        </div>
                    </FormSection>

                    <FormSection title="Hantar ke Ahli Tertentu">
                        <div class="space-y-3">
                            <button
                                type="button"
                                class="flex w-full items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600 transition-colors hover:border-teal-300 hover:text-teal-700"
                                @click="showMemberPicker = !showMemberPicker"
                            >
                                <Search class="h-4 w-4" />
                                Cari dan pilih ahli...
                                <span v-if="form.target_user_ids.length" class="ml-auto text-xs text-teal-600">{{ form.target_user_ids.length }} dipilih</span>
                            </button>

                            <div v-if="showMemberPicker" class="space-y-2 rounded-xl border border-slate-200 p-3">
                                <input
                                    v-model="memberSearch"
                                    type="text"
                                    placeholder="Taip nama untuk mencari..."
                                    class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-teal-500 focus:outline-none"
                                >
                                <div class="max-h-48 space-y-0.5 overflow-y-auto">
                                    <label
                                        v-for="m in filteredMembers"
                                        :key="m.value"
                                        class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-1.5 text-sm hover:bg-slate-50"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="form.target_user_ids.includes(m.value)"
                                            class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                            @change="toggleMember(m.value)"
                                        >
                                        {{ m.label }}
                                    </label>
                                    <p v-if="filteredMembers.length === 0" class="px-3 py-2 text-xs text-slate-400">Tiada hasil.</p>
                                </div>
                            </div>

                            <div v-if="selectedMembers.length && !showMemberPicker" class="flex flex-wrap gap-1">
                                <span
                                    v-for="m in selectedMembers.slice(0, 5)"
                                    :key="m.value"
                                    class="inline-flex items-center gap-1 rounded-full bg-teal-50 px-2 py-0.5 text-xs text-teal-700"
                                >
                                    {{ m.label.split(' (')[0] }}
                                </span>
                                <span v-if="selectedMembers.length > 5" class="text-xs text-slate-400">+{{ selectedMembers.length - 5 }} lagi</span>
                            </div>
                        </div>
                    </FormSection>

                    <div class="flex gap-3">
                        <Button type="button" class="flex-1" :disabled="form.processing" @click="submit">
                            Simpan
                        </Button>
                        <Button :as="Link" href="/admin/announcements" variant="outline" class="flex-1">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>
