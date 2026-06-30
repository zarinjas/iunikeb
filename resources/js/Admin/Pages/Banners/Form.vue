<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import FormSection from '@/Shared/Components/FormSection.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import TextInput from '@/Shared/Components/Form/TextInput.vue';
import ToggleSwitch from '@/Shared/Components/Form/ToggleSwitch.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    banner: { type: Object, default: null },
});

const isEdit = computed(() => Boolean(props.banner));

const form = useForm({
    image: null,
    link_url: props.banner?.link_url || '',
    is_active: props.banner?.is_active ?? true,
});

const previewUrl = ref(props.banner?.image_url || null);
const fileError = ref('');

const handleFileSelect = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (!['image/jpeg', 'image/png'].includes(file.type)) {
        fileError.value = 'Format fail tidak sah. Hanya JPG dan PNG dibenarkan.';
        form.image = null;
        previewUrl.value = props.banner?.image_url || null;
        return;
    }

    if (file.size > 1024 * 1024) {
        fileError.value = 'Saiz fail melebihi 1MB. Sila pilih fail yang lebih kecil.';
        form.image = null;
        previewUrl.value = props.banner?.image_url || null;
        return;
    }

    fileError.value = '';

    const img = new Image();
    const url = URL.createObjectURL(file);
    img.onload = () => {
        form.image = file;
        previewUrl.value = url;
    };
    img.src = url;
};

const submit = () => {
    if (!isEdit.value && !form.image) {
        fileError.value = 'Sila pilih gambar banner.';
        return;
    }

    const formData = new FormData();
    if (form.image) formData.append('image', form.image);
    if (form.link_url) formData.append('link_url', form.link_url);
    formData.append('is_active', form.is_active ? '1' : '0');
    formData.append('_method', isEdit.value ? 'PATCH' : 'POST');

    const onSuccess = () => {
        fileError.value = '';
        previewUrl.value = null;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
    const onError = (errors) => {
        if (errors.image) fileError.value = errors.image;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    form.post(isEdit.value ? `/admin/banners/${props.banner.id}` : '/admin/banners', {
        forceFormData: true,
        onSuccess,
        onError,
    });
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Banner' : 'Tambah Banner'" />
    <AdminLayout>
        <section class="space-y-6">
            <PageHeader
                :title="isEdit ? 'Edit Banner' : 'Tambah Banner'"
                :description="isEdit ? 'Kemas kini banner carousel.' : 'Muat naik banner baharu untuk dashboard ahli.'"
            >
                <template #actions>
                    <Button :as="Link" href="/admin/banners" variant="outline">Kembali</Button>
                </template>
            </PageHeader>

            <FormSection title="Muat Naik Banner">
                <div class="space-y-4">
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                        <p class="mb-3 text-sm text-slate-500">Saiz dicadangkan: <strong>1200×400px</strong>. Maksimum 1MB. Format: JPG/PNG sahaja.</p>

                        <label
                            class="inline-flex cursor-pointer flex-col items-center gap-2 rounded-xl bg-white px-6 py-5 shadow-sm ring-1 ring-slate-200 transition hover:ring-teal-300"
                        >
                            <span class="text-sm font-medium text-teal-600">
                                {{ isEdit ? 'Ganti Gambar' : 'Pilih Gambar' }}
                            </span>
                            <span class="text-xs text-slate-400">Klik untuk pilih fail</span>
                            <input
                                type="file"
                                accept="image/jpeg,image/png"
                                class="hidden"
                                @change="handleFileSelect"
                            />
                        </label>

                        <p v-if="fileError" class="mt-2 text-sm font-medium text-red-500">{{ fileError }}</p>
                        <p v-if="form.errors.image" class="mt-2 text-sm font-medium text-red-500">{{ form.errors.image }}</p>
                    </div>

                    <div v-if="previewUrl" class="overflow-hidden rounded-xl ring-1 ring-slate-200">
                        <p class="bg-slate-50 px-4 py-1.5 text-xs font-medium text-slate-500">Pratonton</p>
                        <img :src="previewUrl" alt="Pratonton banner" class="w-full object-cover" style="aspect-ratio: 3/1" />
                    </div>

                    <TextInput
                        id="banner-link"
                        v-model="form.link_url"
                        label="Pautan (opsional)"
                        placeholder="https://..."
                        :error="form.errors.link_url"
                    />

                    <ToggleSwitch v-if="isEdit" id="banner-active" v-model="form.is_active" label="Aktif" />
                </div>
            </FormSection>

            <div class="flex justify-end gap-3">
                <Button :as="Link" href="/admin/banners" variant="outline">Batal</Button>
                <Button type="button" @click="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Kemas Kini' : 'Simpan') }}
                </Button>
            </div>
        </section>
    </AdminLayout>
</template>
