<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ChevronDown, GripVertical, ImagePlus, Pencil, Plus, Save, ToggleLeft, ToggleRight, Trash2, Upload, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/Shared/Components/ui/button';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
// Using native inputs - shadcn Input/Textarea not yet installed

const props = defineProps({
    section: { type: Object, required: true },
});

const sectionLabels = {
    hero: 'Hero Slider',
    stats: 'Statistik',
    services: 'Perkhidmatan',
    benefit: 'Manfaat Ahli',
    business: 'Perniagaan',
    promotion: 'Promosi',
    membership: 'Keahlian',
    footer: 'Footer',
    member_popup: 'Popup Ahli',
};

const label = sectionLabels[props.section.key] || props.section.key;

const sectionForm = useForm({
    title: props.section.title ?? '',
    subtitle: props.section.subtitle ?? '',
    data: props.section.data ?? {},
    is_active: props.section.is_active,
});

const editingItem = ref(null);
const itemForm = useForm({
    title: '',
    subtitle: '',
    description: '',
    image: null,
    icon: '',
    value: '',
    button_text: '',
    button_url: '',
    extra: null,
    is_active: true,
});

const benefitData = ref({
    background_image: sectionForm.data?.background_image ?? '',
    overlay_enabled: sectionForm.data?.overlay_enabled ?? false,
    overlay_type: sectionForm.data?.overlay_type ?? 'solid',
    overlay_color: sectionForm.data?.overlay_color ?? '#082c3b',
    overlay_opacity: sectionForm.data?.overlay_opacity ?? 70,
    overlay_gradient_color_to: sectionForm.data?.overlay_gradient_color_to ?? 'transparent',
    overlay_gradient_angle: sectionForm.data?.overlay_gradient_angle ?? 180,
    decorative_enabled: sectionForm.data?.decorative_enabled ?? false,
    decorative_color: sectionForm.data?.decorative_color ?? '#0d9488',
    decorative_angle: sectionForm.data?.decorative_angle ?? 135,
});

const overlayPanelOpen = ref(true);
const decorativePanelOpen = ref(false);

function hexToRgba(hex, alpha) {
    const c = hex.replace('#', '');
    const r = parseInt(c.substring(0, 2), 16);
    const g = parseInt(c.substring(2, 4), 16);
    const b = parseInt(c.substring(4, 6), 16);
    return `rgba(${r},${g},${b},${alpha})`;
}

const overlayPreviewStyle = computed(() => {
    const d = benefitData.value;
    if (!d.overlay_enabled) return {};
    const opacity = d.overlay_opacity != null ? d.overlay_opacity / 100 : 1;
    if (d.overlay_type === 'gradient') {
        const angle = d.overlay_gradient_angle || 180;
        const c1 = d.overlay_color ? hexToRgba(d.overlay_color, opacity) : 'transparent';
        return { backgroundImage: `linear-gradient(${angle}deg, ${c1}, ${d.overlay_gradient_color_to || 'transparent'})` };
    }
    return { backgroundColor: hexToRgba(d.overlay_color, opacity) };
});

const directionLabels = {
    0: '↑ Ke Atas', 45: '↗ Atas Kanan', 90: '→ Ke Kanan',
    135: '↘ Bawah Kanan', 180: '↓ Ke Bawah', 225: '↙ Bawah Kiri',
    270: '← Ke Kiri', 315: '↖ Atas Kiri',
};

function directionLabel(angle) {
    const key = Object.keys(directionLabels).reduce((a, b) =>
        Math.abs(Number(a) - angle) < Math.abs(Number(b) - angle) ? a : b
    );
    return directionLabels[key];
}

function saveBenefitSettings() {
    router.put(
        `/admin/frontpage/sections/${props.section.key}`,
        { data: { ...benefitData.value } },
        { preserveScroll: true, preserveState: true },
    );
}

function saveSection() {
    sectionForm.put(`/admin/frontpage/sections/${props.section.key}`);
}

function startNewItem() {
    itemForm.reset();
    editingItem.value = -1;
    previewUrl.value = null;
}

function editItem(item) {
    itemForm.defaults({
        title: item.title ?? '',
        subtitle: item.subtitle ?? '',
        description: item.description ?? '',
        image: item.image || null,
        icon: item.icon ?? '',
        value: item.value ?? '',
        button_text: item.button_text ?? '',
        button_url: item.button_url ?? '',
        extra: item.extra ?? null,
        is_active: item.is_active,
    });
    itemForm.reset();
    editingItem.value = item.id;
    previewUrl.value = null;
}

function cancelEdit() {
    editingItem.value = null;
    previewUrl.value = null;
}

function saveItem() {
    if (editingItem.value === -1) {
        itemForm.post(`/admin/frontpage/sections/${props.section.key}/items`, {
            preserveScroll: true,
            onSuccess: () => { editingItem.value = null; previewUrl.value = null; },
        });
    } else {
        itemForm.put(`/admin/frontpage/sections/${props.section.key}/items/${editingItem.value}`, {
            preserveScroll: true,
            onSuccess: () => { editingItem.value = null; previewUrl.value = null; },
        });
    }
}

function deleteItem(item) {
    if (confirm('Padam item ini?')) {
        itemForm.delete(`/admin/frontpage/sections/${props.section.key}/items/${item.id}`, {
            preserveScroll: true,
        });
    }
}

const previewUrl = ref(null);
const uploading = ref(false);

async function handleFilePick(event) {
    const file = event.target.files[0];
    if (!file) return;
    // Show local preview
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = URL.createObjectURL(file);
    uploading.value = true;
    // Upload file immediately via fetch to simple endpoint
    const fd = new FormData();
    fd.append('file', file);
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) fd.append('_token', token);
    try {
        const res = await fetch('/admin/frontpage/upload-image', {
            method: 'POST',
            body: fd,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        if (json.path) {
            itemForm.image = json.path;
            previewUrl.value = null;
        }
    } catch {
        alert('Gagal muat naik gambar.');
    } finally {
        uploading.value = false;
    }
}

const benefitBgPreviewUrl = ref(null);
const benefitBgUploading = ref(false);

async function handleBenefitBgPick(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (benefitBgPreviewUrl.value) URL.revokeObjectURL(benefitBgPreviewUrl.value);
    benefitBgPreviewUrl.value = URL.createObjectURL(file);
    benefitBgUploading.value = true;
    const fd = new FormData();
    fd.append('file', file);
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) fd.append('_token', token);
    try {
        const res = await fetch('/admin/frontpage/upload-image', { method: 'POST', body: fd });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        if (json.path) {
            benefitData.value.background_image = json.path;
            benefitBgPreviewUrl.value = null;
        }
    } catch {
        alert('Gagal muat naik gambar.');
    } finally {
        benefitBgUploading.value = false;
    }
}

function benefitBgPreview() {
    if (benefitBgPreviewUrl.value) return benefitBgPreviewUrl.value;
    const bg = benefitData.value.background_image;
    if (!bg) return null;
    return bg.startsWith('http') ? bg : '/storage/' + bg;
}

function previewImage() {
    if (previewUrl.value) return previewUrl.value;
    if (itemForm.image && typeof itemForm.image === 'string') {
        return itemForm.image.startsWith('http') ? itemForm.image : '/storage/' + itemForm.image;
    }
    return null;
}
// Field helpers per section type
const heroFields = props.section.key === 'hero';
const statsFields = props.section.key === 'stats';
const serviceFields = props.section.key === 'services';
const benefitFields = props.section.key === 'benefit';
const businessFields = props.section.key === 'business';
const promotionFields = props.section.key === 'promotion';
const membershipFields = props.section.key === 'membership';
const footerFields = props.section.key === 'footer';
const popupFields = props.section.key === 'member_popup';
</script>

<template>
    <Head :title="`Edit ${label}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Back -->
            <Link href="/admin/frontpage" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800">
                <ArrowLeft class="h-4 w-4" />
                Kembali ke senarai seksyen
            </Link>

            <!-- Section Header -->
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ label }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ section.items?.length || 0 }} item</p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm transition"
                    :class="sectionForm.is_active ? 'border-green-200 bg-green-50 text-green-700' : 'border-slate-200 bg-slate-50 text-slate-500'"
                    @click="sectionForm.is_active = !sectionForm.is_active; saveSection()"
                >
                    <component :is="sectionForm.is_active ? ToggleRight : ToggleLeft" class="h-4 w-4" />
                    {{ sectionForm.is_active ? 'Aktif' : 'Tidak Aktif' }}
                </button>
            </div>

            <!-- Section settings -->
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500">Tajuk Seksyen</label>
                        <input v-model="sectionForm.title" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500">Subtajuk</label>
                        <input v-model="sectionForm.subtitle" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                </div>
                <div class="mt-3 flex justify-end">
                    <Button size="sm" @click="saveSection">
                        <Save class="mr-1 h-4 w-4" />
                        Simpan Seksyen
                    </Button>
                </div>
            </div>

            <!-- ========== BENEFIT BACKGROUND & OVERLAY ========== -->
            <div v-if="benefitFields" class="space-y-3">

                <!-- Background Image -->
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Background</h3>
                    <div class="flex items-center gap-3">
                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-slate-300 px-4 py-2 text-sm text-slate-500 transition hover:border-teal-300 hover:text-teal-600">
                            <Upload class="h-4 w-4" />
                            <span>{{ benefitBgPreview() ? 'Tukar' : 'Pilih Imej' }}</span>
                            <input type="file" accept="image/*" class="hidden" @change="handleBenefitBgPick" />
                        </label>
                        <button v-if="benefitBgPreview()" type="button" class="text-sm text-red-500 hover:text-red-600" @click="benefitData.background_image = ''; benefitBgPreviewUrl = null">Buang</button>
                    </div>
                    <div v-if="benefitBgPreview()" class="relative mt-2">
                        <img :src="benefitBgPreview()" class="h-28 w-full rounded-lg border object-cover" />
                        <span v-if="benefitBgUploading" class="absolute bottom-2 right-2 rounded bg-black/60 px-2 py-0.5 text-xs text-white">Muat naik...</span>
                    </div>
                </div>

                <!-- Background Overlay Panel -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <button type="button" class="flex w-full items-center justify-between px-4 py-3 text-left transition hover:bg-slate-50" @click="overlayPanelOpen = !overlayPanelOpen">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Background Overlay</span>
                        <ChevronDown class="h-4 w-4 text-slate-400 transition" :class="overlayPanelOpen ? 'rotate-180' : ''" />
                    </button>
                    <div v-show="overlayPanelOpen" class="border-t border-slate-100 px-4 pb-4 pt-3">
                        <!-- Enable toggle -->
                        <div class="mb-4 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                            <span class="text-sm font-medium text-slate-700">Enable Overlay</span>
                            <button
                                type="button"
                                class="relative h-6 w-11 rounded-full transition"
                                :class="benefitData.overlay_enabled ? 'bg-teal-600' : 'bg-slate-300'"
                                @click="benefitData.overlay_enabled = !benefitData.overlay_enabled"
                            >
                                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition" :class="benefitData.overlay_enabled ? 'translate-x-5' : ''" />
                            </button>
                        </div>

                        <template v-if="benefitData.overlay_enabled">
                            <!-- Type selector -->
                            <div class="mb-4 flex gap-1 rounded-lg bg-slate-100 p-0.5">
                                <button
                                    type="button"
                                    class="flex-1 rounded-md px-3 py-1.5 text-xs font-medium transition"
                                    :class="benefitData.overlay_type === 'solid' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                    @click="benefitData.overlay_type = 'solid'"
                                >Solid</button>
                                <button
                                    type="button"
                                    class="flex-1 rounded-md px-3 py-1.5 text-xs font-medium transition"
                                    :class="benefitData.overlay_type === 'gradient' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                    @click="benefitData.overlay_type = 'gradient'"
                                >Gradient</button>
                            </div>

                            <!-- Solid Mode -->
                            <template v-if="benefitData.overlay_type === 'solid'">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-500">Color</label>
                                    <div class="flex items-center gap-2">
                                        <input v-model="benefitData.overlay_color" type="color" class="h-9 w-12 cursor-pointer rounded border border-slate-200" />
                                        <input v-model="benefitData.overlay_color" class="h-9 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono" />
                                    </div>
                                </div>
                            </template>

                            <!-- Gradient Mode -->
                            <template v-if="benefitData.overlay_type === 'gradient'">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-500">Color 1</label>
                                        <input v-model="benefitData.overlay_color" type="color" class="h-9 w-full cursor-pointer rounded border border-slate-200" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-500">Color 2</label>
                                        <div class="flex items-center gap-2">
                                            <span class="h-9 w-12 shrink-0 rounded border border-slate-200" :style="{ background: benefitData.overlay_gradient_color_to || 'transparent' }" />
                                            <input v-model="benefitData.overlay_gradient_color_to" class="h-9 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono" placeholder="transparent" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-medium text-slate-500">Direction — {{ directionLabel(benefitData.overlay_gradient_angle) }}</label>
                                    <input v-model.number="benefitData.overlay_gradient_angle" type="range" min="0" max="360" class="w-full accent-teal-600" />
                                    <div class="mt-1 flex justify-between text-[10px] text-slate-400">
                                        <span>0°</span><span>90°</span><span>180°</span><span>270°</span><span>360°</span>
                                    </div>
                                </div>
                            </template>

                            <!-- Opacity (common) -->
                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-medium text-slate-500">Opacity ({{ benefitData.overlay_opacity }}%)</label>
                                <input v-model.number="benefitData.overlay_opacity" type="range" min="0" max="100" class="w-full accent-teal-600" />
                            </div>

                            <!-- Live Preview -->
                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-medium text-slate-500">Preview</label>
                                <div class="relative h-20 overflow-hidden rounded-lg border border-slate-200 bg-[radial-gradient(circle,#e2e8f0_1px,transparent_1px)] bg-[length:12px_12px]">
                                    <div v-if="benefitBgPreview()" class="absolute inset-0 bg-cover bg-center" :style="{ backgroundImage: `url(${benefitBgPreview()})` }" />
                                    <div class="absolute inset-0" :style="overlayPreviewStyle" />
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Decorative Gradient Panel -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <button type="button" class="flex w-full items-center justify-between px-4 py-3 text-left transition hover:bg-slate-50" @click="decorativePanelOpen = !decorativePanelOpen">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Decorative Gradient</span>
                        <ChevronDown class="h-4 w-4 text-slate-400 transition" :class="decorativePanelOpen ? 'rotate-180' : ''" />
                    </button>
                    <div v-show="decorativePanelOpen" class="border-t border-slate-100 px-4 pb-4 pt-3">
                        <div class="mb-4 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                            <span class="text-sm font-medium text-slate-700">Enable</span>
                            <button
                                type="button"
                                class="relative h-6 w-11 rounded-full transition"
                                :class="benefitData.decorative_enabled ? 'bg-teal-600' : 'bg-slate-300'"
                                @click="benefitData.decorative_enabled = !benefitData.decorative_enabled"
                            >
                                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition" :class="benefitData.decorative_enabled ? 'translate-x-5' : ''" />
                            </button>
                        </div>
                        <template v-if="benefitData.decorative_enabled">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-500">Color</label>
                                    <div class="flex items-center gap-2">
                                        <input v-model="benefitData.decorative_color" type="color" class="h-9 w-12 cursor-pointer rounded border border-slate-200" />
                                        <input v-model="benefitData.decorative_color" class="h-9 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm font-mono" />
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-500">Angle ({{ benefitData.decorative_angle }}°)</label>
                                    <input v-model.number="benefitData.decorative_angle" type="range" min="0" max="360" class="w-full accent-teal-600" />
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button size="sm" @click="saveBenefitSettings">
                        <Save class="mr-1 h-4 w-4" />
                        Simpan
                    </Button>
                </div>
            </div>

            <!-- Items List -->
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Item</h2>
                <Button v-if="editingItem === null" size="sm" @click="startNewItem">
                    <Plus class="mr-1 h-4 w-4" />
                    Tambah Item
                </Button>
            </div>

            <!-- New/Edit Item Form -->
            <div v-if="editingItem !== null" class="rounded-xl border border-teal-200 bg-teal-50/30 p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-semibold text-teal-800">
                        {{ editingItem === -1 ? 'Item Baru' : 'Edit Item' }}
                    </h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600" @click="cancelEdit">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div v-if="heroFields || serviceFields || businessFields || promotionFields || popupFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Imej</label>
                        <div class="flex items-center gap-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-slate-300 px-4 py-2 text-sm text-slate-500 hover:border-teal-300 hover:text-teal-600">
                                <Upload class="h-4 w-4" />
                                <span>{{ previewImage() ? 'Tukar' : 'Pilih Imej' }}</span>
                                <input type="file" accept="image/*" class="hidden" @change="handleFilePick" />
                            </label>
                            <button v-if="previewImage()" type="button" class="text-sm text-red-500 hover:text-red-600" @click="itemForm.image = null; previewUrl = null">Buang</button>
                        </div>
                        <p v-if="popupFields" class="mt-1.5 text-xs text-slate-400 leading-relaxed">
                            Saiz terbaik: 800×600px · Maks 2MB · Format: JPG/PNG/WebP
                        </p>
                        <div v-if="previewImage()" class="relative mt-2 w-fit">
                            <img :src="previewImage()" class="h-32 rounded-lg border object-cover" />
                            <span v-if="uploading" class="absolute bottom-1 right-1 rounded bg-black/60 px-2 py-0.5 text-xs text-white">Muat naik...</span>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500">Tajuk <span v-if="popupFields" class="text-slate-300">(opsional)</span></label>
                        <input v-model="itemForm.title" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>

                    <div v-if="heroFields || statsFields || serviceFields || businessFields || promotionFields || popupFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Subtajuk <span v-if="popupFields" class="text-slate-300">(opsional)</span></label>
                        <input v-model="itemForm.subtitle" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>

                    <div v-if="heroFields || serviceFields || businessFields || promotionFields || popupFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Kandungan <span v-if="popupFields" class="text-slate-300">(opsional)</span></label>
                        <textarea v-model="itemForm.description" class="min-h-[80px] w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div v-if="statsFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Nilai</label>
                        <input v-model="itemForm.value" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div v-if="benefitFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Ikon (nama Lucide)</label>
                        <input v-model="itemForm.icon" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div v-if="statsFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Label</label>
                        <input v-model="itemForm.title" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div v-if="servicesFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Ikon (nama Lucide)</label>
                        <input v-model="itemForm.icon" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div v-if="heroFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Label Badge</label>
                        <input v-model="itemForm.title" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>

                    <div v-if="heroFields || servicesFields || businessFields || promotionFields || membershipFields || popupFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">Teks Butang <span v-if="popupFields" class="text-slate-300">(opsional)</span></label>
                        <input v-model="itemForm.button_text" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>

                    <div v-if="heroFields || servicesFields || businessFields || promotionFields || membershipFields || popupFields">
                        <label class="mb-1 block text-xs font-medium text-slate-500">URL Butang <span v-if="popupFields" class="text-slate-300">(opsional)</span></label>
                        <input v-model="itemForm.button_url" class="h-9 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-500">
                        <input v-model="itemForm.is_active" type="checkbox" class="rounded" />
                        Aktif
                    </label>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" @click="cancelEdit">Batal</Button>
                        <Button size="sm" :disabled="uploading || itemForm.processing" @click="saveItem">
                            <Save class="mr-1 h-4 w-4" />
                            {{ editingItem === -1 ? 'Tambah' : 'Simpan' }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div v-if="section.items?.length" class="rounded-xl border border-slate-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs font-medium text-slate-500">
                                <th class="w-10 px-4 py-3">#</th>
                                <th v-if="heroFields || serviceFields || businessFields || promotionFields || popupFields" class="px-4 py-3">Imej</th>
                                <th class="px-4 py-3">Tajuk</th>
                                <th v-if="statsFields" class="px-4 py-3">Nilai</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="w-24 px-4 py-3 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in section.items"
                                :key="item.id"
                                class="border-b border-slate-100 transition hover:bg-slate-50"
                                :class="editingItem === item.id ? 'bg-teal-50/50' : ''"
                            >
                                <td class="px-4 py-3 text-slate-400">{{ index + 1 }}</td>
                                <td v-if="heroFields || serviceFields || businessFields || promotionFields || popupFields" class="px-4 py-3">
                                    <img
                                        v-if="item.image && typeof item.image === 'string'"
                                        :src="item.image.startsWith('http') ? item.image : '/storage/' + item.image"
                                        class="h-10 w-16 rounded object-cover"
                                    />
                                    <span v-else class="text-slate-300">—</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ item.title || item.value || '—' }}
                                </td>
                                <td v-if="statsFields" class="px-4 py-3 font-semibold text-teal-700">
                                    {{ item.value }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                                    >
                                        {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                                            title="Edit"
                                            @click="editItem(item)"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded p-1 text-slate-400 hover:bg-red-50 hover:text-red-500"
                                            title="Padam"
                                            @click="deleteItem(item)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="!section.items?.length && editingItem === null"
                class="rounded-xl border border-dashed border-slate-200 p-12 text-center"
            >
                <ImagePlus class="mx-auto h-10 w-10 text-slate-300" />
                <p class="mt-3 text-sm text-slate-500">Tiada item. Klik "Tambah Item" untuk mula.</p>
            </div>
        </div>
    </AdminLayout>
</template>
