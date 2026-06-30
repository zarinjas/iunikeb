<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import FormSection from '@/Shared/Components/FormSection.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import TextInput from '@/Shared/Components/Form/TextInput.vue';
import SelectInput from '@/Shared/Components/Form/SelectInput.vue';
import ToggleSwitch from '@/Shared/Components/Form/ToggleSwitch.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    poster: { type: Object, default: null },
});

const isEdit = computed(() => Boolean(props.poster));

const form = useForm({
    title: props.poster?.title || '',
    alt_text: props.poster?.alt_text || '',
    image_path: props.poster?.image_path || '',
    link_url: props.poster?.link_url || '',
    type: props.poster?.type || 'banner',
    audience: props.poster?.audience || 'members',
    is_active: props.poster?.is_active ?? true,
    sort_order: props.poster?.sort_order ?? 0,
});

const submit = () => {
    const onSuccess = () => window.scrollTo({ top: 0, behavior: 'smooth' });
    const onError = () => window.scrollTo({ top: 0, behavior: 'smooth' });

    if (isEdit.value) {
        form.patch(`/admin/posters/${props.poster.id}`, { onSuccess, onError });
    } else {
        form.post('/admin/posters', { onSuccess, onError });
    }
};

const typeOptions = [
    { value: 'banner', label: 'Banner' },
    { value: 'poster', label: 'Poster' },
];

const audienceOptions = [
    { value: 'members', label: 'Ahli Sahaja' },
    { value: 'public', label: 'Awam' },
    { value: 'both', label: 'Semua' },
];

const previewUrl = computed(() => {
    if (!form.image_path) return null;
    if (form.image_path.startsWith('http')) return form.image_path;
    return '/storage/' + form.image_path;
});
</script>

<template>
    <Head :title="isEdit ? 'Edit Poster' : 'Tambah Poster'" />
    <AdminLayout>
        <section class="space-y-6">
            <PageHeader :title="isEdit ? 'Edit Poster' : 'Tambah Poster'" :description="isEdit ? 'Kemas kini maklumat poster.' : 'Tambah poster atau banner baharu.'">
                <template #actions>
                    <Button :as="Link" href="/admin/posters" variant="outline">Kembali</Button>
                </template>
            </PageHeader>

            <FormSection title="Maklumat Poster" :columns="2">
                <div class="md:col-span-2">
                    <TextInput id="poster-title" v-model="form.title" label="Tajuk" :error="form.errors.title" />
                </div>

                <SelectInput id="poster-type" v-model="form.type" label="Jenis" :options="typeOptions" :error="form.errors.type" />
                <SelectInput id="poster-audience" v-model="form.audience" label="Audien" :options="audienceOptions" :error="form.errors.audience" />

                <div class="md:col-span-2">
                    <TextInput id="poster-image" v-model="form.image_path" label="Path Gambar" placeholder="media/... atau URL penuh" :error="form.errors.image_path" help="Gunakan Media Library untuk muat naik, kemudian salin path gambar." />
                    <img v-if="previewUrl" :src="previewUrl" :alt="form.title" class="mt-2 max-h-40 rounded-xl object-cover ring-1 ring-slate-200" />
                </div>

                <div class="md:col-span-2">
                    <TextInput id="poster-link" v-model="form.link_url" label="Pautan (opsional)" placeholder="https://..." :error="form.errors.link_url" />
                </div>

                <div class="md:col-span-2">
                    <TextInput id="poster-alt" v-model="form.alt_text" label="Teks Alternatif" :error="form.errors.alt_text" />
                </div>

                <TextInput id="poster-sort" v-model="form.sort_order" label="Susunan" type="number" :error="form.errors.sort_order" />
                <ToggleSwitch id="poster-active" v-model="form.is_active" label="Aktif" />
            </FormSection>

            <div class="flex justify-end gap-3">
                <Button :as="Link" href="/admin/posters" variant="outline">Batal</Button>
                <Button type="button" @click="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Kemas Kini' : 'Simpan') }}
                </Button>
            </div>
        </section>
    </AdminLayout>
</template>
