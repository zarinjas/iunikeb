<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, CheckCircle2, Loader2, Search, ShieldCheck, Sparkles, UserCheck, Users } from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import PublicLayout from '@/Public/Layouts/PublicLayout.vue';
import FormSection from '@/Shared/Components/FormSection.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import ReferrerSearchInput from '@/Shared/Components/Form/ReferrerSearchInput.vue';
import SelectInput from '@/Shared/Components/Form/SelectInput.vue';
import SignaturePad from '@/Shared/Components/SignaturePad.vue';
import TextInput from '@/Shared/Components/Form/TextInput.vue';
import TextareaInput from '@/Shared/Components/Form/TextareaInput.vue';
import { Button } from '@/Shared/Components/ui/button';

const props = defineProps({
    genderOptions: { type: Array, required: true },
});

const currentStep = ref(1);
const totalSteps = 3;

const steps = [
    { number: 1, title: 'Maklumat Peribadi', description: 'Butiran asas pemohon' },
    { number: 2, title: 'Alamat & Pekerjaan', description: 'Maklumat tambahan pemohon' },
    { number: 3, title: 'Semakan & Tandatangan', description: 'Pengesahan permohonan' },
];

const form = useForm({
    full_name: '',
    identity_no: '',
    email: '',
    phone: '',
    address_line_1: '',
    city: '',
    state: '',
    postcode: '',
    date_of_birth: '',
    gender: '',
    occupation: '',
    employer_name: '',
    notes: '',
    referred_by_member_id: null,
    digital_signature: '',
});

// --- Step navigation ---
const isStep1Valid = computed(() => {
    return form.full_name.trim()
        && form.identity_no.trim()
        && form.email.trim()
        && form.phone.trim()
        && form.date_of_birth
        && form.gender;
});

const goToStep = (step) => {
    if (step < 1 || step > totalSteps) return;
    if (step === 2 && !isStep1Valid.value) return;
    currentStep.value = step;
    nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }));
};

// --- Submit ---
const submit = () => {
    if (form.identity_no) {
        form.identity_no = form.identity_no.replace(/\D/g, '').replace(/^(\d{6})(\d{2})(\d{4})$/, '$1-$2-$3');
    }
    if (form.phone) {
        form.phone = form.phone.replace(/\D/g, '');
    }
    form.post('/membership/apply', {
        onSuccess: () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            form.reset('notes');
        },
        onError: () => {
            currentStep.value = 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    });
};

// --- Batal with confirmation ---
const showConfirmCancel = ref(false);
const handleCancel = () => {
    const isDirty = [form.full_name, form.identity_no, form.email, form.phone, form.address_line_1, form.city, form.state, form.postcode, form.date_of_birth, form.gender, form.occupation, form.employer_name, form.notes, form.referred_by_member_id, form.digital_signature].some(v => v !== '' && v !== null && v !== undefined);
    if (isDirty) {
        showConfirmCancel.value = true;
    } else {
        window.location.href = '/';
    }
};
const confirmCancel = () => {
    showConfirmCancel.value = false;
    form.reset();
    currentStep.value = 1;
};

// --- IC auto-format + DOB auto-fill ---
const icRaw = ref('');
const dobAutoFilled = ref(false);

watch(() => form.identity_no, (val) => {
    if (!val) {
        icRaw.value = '';
        return;
    }
    const digits = val.replace(/\D/g, '');
    if (digits.length >= 7) {
        const formatted = `${digits.substring(0, 6)}-${digits.substring(6, 8)}-${digits.substring(8, 12)}`;
        icRaw.value = formatted;
        form.identity_no = formatted;
    }

    if (digits.length >= 6) {
        const day = parseInt(digits.substring(4, 6), 10);
        const month = parseInt(digits.substring(2, 4), 10);
        const shortYear = parseInt(digits.substring(0, 2), 10);
        if (day && month && shortYear !== undefined && month >= 1 && month <= 12 && day >= 1 && day <= 31) {
            const fullYear = shortYear > 50 ? 1900 + shortYear : 2000 + shortYear;
            const monthStr = String(month).padStart(2, '0');
            const dayStr = String(day).padStart(2, '0');
            form.date_of_birth = `${fullYear}-${monthStr}-${dayStr}`;
            dobAutoFilled.value = true;
            setTimeout(() => { dobAutoFilled.value = false; }, 2500);
        }
    }
});

// --- Postcode auto-fill ---
const postcodeLoading = ref(false);
const postcodeAutoFilled = ref(false);
let postcodeTimeout;

watch(() => form.postcode, (val) => {
    clearTimeout(postcodeTimeout);
    if (!val || val.length !== 5) return;
    postcodeLoading.value = true;
    postcodeTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/postcode/${val}`);
            if (!res.ok) return;
            const data = await res.json();
            if (data.city || data.state) {
                if (data.city) form.city = data.city;
                if (data.state) form.state = data.state;
                postcodeAutoFilled.value = true;
                setTimeout(() => { postcodeAutoFilled.value = false; }, 2500);
            }
        } catch {
            // ignore
        } finally {
            postcodeLoading.value = false;
        }
    }, 300);
});

// --- Referral code from URL ---
const handleReferralCode = async (code) => {
    if (!code) return;
    try {
        const res = await fetch(`/api/members/search?code=${encodeURIComponent(code)}`);
        const data = await res.json();
        if (data.data && data.data.length > 0) {
            form.referred_by_member_id = data.data[0].id;
        }
    } catch {
        // ignore invalid codes
    }
};

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const refCode = params.get('ref');
    if (refCode) {
        handleReferralCode(refCode);
    }
});

// --- Gender helpers ---
const genderLabel = computed(() => {
    const opt = props.genderOptions.find(o => o.value === form.gender);
    return opt ? opt.label : '-';
});

// --- Review data ---
const reviewFields = computed(() => [
    { label: 'Nama penuh', value: form.full_name },
    { label: 'No. kad pengenalan', value: form.identity_no },
    { label: 'E-mel', value: form.email },
    { label: 'No. telefon', value: form.phone },
    { label: 'Tarikh lahir', value: form.date_of_birth },
    { label: 'Jantina', value: genderLabel.value },
    { label: 'Alamat', value: form.address_line_1 },
    { label: 'Poskod', value: form.postcode },
    { label: 'Bandar', value: form.city },
    { label: 'Negeri', value: form.state },
]);
</script>

<template>
    <Head title="Permohonan Keahlian" />

    <PublicLayout>
        <section class="bg-gradient-to-br from-emerald-50 via-white to-blue-50">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-10 px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
                <PageHeader
                    title="Permohonan Keahlian"
                    description="Lengkapkan borang di bawah untuk menghantar permohonan keahlian baharu. Maklumat anda akan disemak oleh pihak admin koperasi."
                />

                <!-- Step Progress -->
                <nav aria-label="Progress" class="mx-auto w-full max-w-2xl">
                    <ol class="flex items-center">
                        <li v-for="(step, idx) in steps" :key="step.number" class="relative flex-1" :class="idx < steps.length - 1 ? 'pr-8 sm:pr-12' : ''">
                            <div v-if="idx < steps.length - 1" class="absolute right-0 top-4 hidden h-0.5 w-full sm:block" :class="currentStep > step.number ? 'bg-teal-600' : 'bg-slate-200'" aria-hidden="true" />
                            <button
                                type="button"
                                class="group flex w-full flex-col items-center gap-2 sm:flex-row sm:gap-3"
                                :disabled="step.number > 1 && step.number === 2 && !isStep1Valid"
                                @click="step.number <= currentStep || (step.number === 2 && isStep1Valid) ? goToStep(step.number) : null"
                            >
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold transition"
                                    :class="currentStep === step.number
                                        ? 'bg-teal-700 text-white shadow-sm'
                                        : currentStep > step.number
                                            ? 'bg-teal-600 text-white'
                                            : 'border-2 border-slate-300 bg-white text-slate-500'"
                                >
                                    <CheckCircle2 v-if="currentStep > step.number" class="h-4 w-4" />
                                    <span v-else>{{ step.number }}</span>
                                </span>
                                <div class="text-center sm:text-left">
                                    <span
                                        class="block text-xs font-semibold"
                                        :class="currentStep === step.number ? 'text-teal-800' : currentStep > step.number ? 'text-teal-700' : 'text-slate-400'"
                                    >{{ step.title }}</span>
                                    <span class="hidden text-[11px] text-slate-400 sm:block">{{ step.description }}</span>
                                </div>
                            </button>
                        </li>
                    </ol>
                </nav>

                <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <form class="space-y-6" @submit.prevent="currentStep < totalSteps ? goToStep(currentStep + 1) : submit()">
                        <!-- STEP 1: Maklumat Peribadi -->
                        <div v-show="currentStep === 1">
                            <FormSection title="Maklumat Peribadi" description="Sila masukkan butiran asas seperti dalam rekod pengenalan rasmi." :columns="2">
                                <TextInput id="apply-full-name" v-model="form.full_name" label="Nama penuh" required :error="form.errors.full_name" autocomplete="name" />
                                <div class="w-full space-y-2">
                                    <label for="apply-identity-no" class="text-sm font-medium text-slate-800">
                                        No. kad pengenalan
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="apply-identity-no"
                                        v-model="form.identity_no"
                                        type="text"
                                        autocomplete="off"
                                        inputmode="numeric"
                                        maxlength="14"
                                        placeholder="XXXXXX-XX-XXXX"
                                        class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                        :aria-invalid="Boolean(form.errors.identity_no)"
                                    />
                                    <p class="text-xs leading-5 text-slate-500">Format: XXXXXX-XX-XXXX. Tarikh lahir akan diisi automatik.</p>
                                    <p v-if="form.errors.identity_no" class="text-sm text-red-700" v-html="form.errors.identity_no" />
                                </div>
                                <TextInput id="apply-email" v-model="form.email" label="E-mel" required type="email" :error="form.errors.email" autocomplete="email" />
                                <div class="w-full space-y-2">
                                    <label for="apply-phone" class="text-sm font-medium text-slate-800">
                                        No. telefon
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="apply-phone"
                                        v-model="form.phone"
                                        type="tel"
                                        autocomplete="tel"
                                        placeholder="0123456789"
                                        class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                        :aria-invalid="Boolean(form.errors.phone)"
                                    />
                                    <p class="text-xs leading-5 text-slate-500">Contoh: 0123456789</p>
                                    <p v-if="form.errors.phone" class="text-sm text-red-700">{{ form.errors.phone }}</p>
                                </div>
                                <div class="w-full space-y-2">
                                    <label for="apply-date-of-birth" class="text-sm font-medium text-slate-800">
                                        Tarikh lahir
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="apply-date-of-birth"
                                            v-model="form.date_of_birth"
                                            type="date"
                                            class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                            :class="{ 'border-teal-500 bg-teal-50/50 ring-2 ring-teal-500/20': dobAutoFilled }"
                                            :aria-invalid="Boolean(form.errors.date_of_birth)"
                                        />
                                        <Transition
                                            enter-active-class="transition ease-out duration-200"
                                            enter-from-class="opacity-0 -translate-y-1"
                                            enter-to-class="opacity-100 translate-y-0"
                                            leave-active-class="transition ease-in duration-150"
                                            leave-from-class="opacity-100"
                                            leave-to-class="opacity-0"
                                        >
                                            <span v-if="dobAutoFilled" class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1 rounded-full bg-teal-100 px-2 py-0.5 text-[11px] font-medium text-teal-800">
                                                <Sparkles class="h-3 w-3" /> Dijana dari IC
                                            </span>
                                        </Transition>
                                    </div>
                                    <p v-if="form.errors.date_of_birth" class="text-sm text-red-700">{{ form.errors.date_of_birth }}</p>
                                </div>
                                <SelectInput id="apply-gender" v-model="form.gender" label="Jantina" required :options="genderOptions" :error="form.errors.gender" />
                            </FormSection>
                        </div>

                        <!-- STEP 2: Alamat & Pekerjaan -->
                        <div v-show="currentStep === 2">
                            <FormSection title="Alamat" description="Maklumat alamat tempat tinggal semasa." :columns="2">
                                <div class="md:col-span-2">
                                    <TextareaInput id="apply-address-line-1" v-model="form.address_line_1" label="Alamat" required :rows="3" :error="form.errors.address_line_1" />
                                </div>
                                <div class="w-full space-y-2">
                                    <label for="apply-postcode" class="text-sm font-medium text-slate-800">
                                        Poskod
                                        <span class="text-xs font-normal text-slate-400">(Pilihan)</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="apply-postcode"
                                            v-model="form.postcode"
                                            type="text"
                                            inputmode="numeric"
                                            maxlength="5"
                                            placeholder="Contoh: 43000"
                                            class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white pl-3 pr-10 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                            :class="{ 'border-teal-500 bg-teal-50/50 ring-2 ring-teal-500/20': postcodeAutoFilled || postcodeLoading }"
                                            :aria-invalid="Boolean(form.errors.postcode)"
                                        />
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <Loader2 v-if="postcodeLoading" class="h-4 w-4 animate-spin text-teal-600" />
                                            <Search v-else class="h-4 w-4 text-slate-400" />
                                        </span>
                                    </div>
                                    <p class="text-xs leading-5 text-slate-500">Bandar dan negeri akan diisi automatik.</p>
                                    <p v-if="form.errors.postcode" class="text-sm text-red-700">{{ form.errors.postcode }}</p>
                                </div>
                                <div class="w-full space-y-2">
                                    <label for="apply-city" class="text-sm font-medium text-slate-800">
                                        Bandar
                                        <span class="text-xs font-normal text-slate-400">(Pilihan)</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="apply-city"
                                            v-model="form.city"
                                            type="text"
                                            class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                            :class="{ 'border-teal-500 bg-teal-50/50 ring-2 ring-teal-500/20': postcodeAutoFilled }"
                                            :aria-invalid="Boolean(form.errors.city)"
                                        />
                                    </div>
                                    <p v-if="form.errors.city" class="text-sm text-red-700">{{ form.errors.city }}</p>
                                </div>
                                <div class="w-full space-y-2">
                                    <label for="apply-state" class="text-sm font-medium text-slate-800">
                                        Negeri
                                        <span class="text-xs font-normal text-slate-400">(Pilihan)</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="apply-state"
                                            v-model="form.state"
                                            type="text"
                                            class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                            :class="{ 'border-teal-500 bg-teal-50/50 ring-2 ring-teal-500/20': postcodeAutoFilled }"
                                            :aria-invalid="Boolean(form.errors.state)"
                                        />
                                    </div>
                                    <p v-if="form.errors.state" class="text-sm text-red-700">{{ form.errors.state }}</p>
                                </div>
                            </FormSection>

                            <FormSection title="Maklumat Pekerjaan" description="Maklumat ini membantu koperasi memahami latar belakang pemohon." :columns="2" class="mt-6">
                                <div class="w-full space-y-2">
                                    <label for="apply-occupation" class="text-sm font-medium text-slate-800">
                                        Jawatan
                                        <span class="text-xs font-normal text-slate-400">(Pilihan)</span>
                                    </label>
                                    <input
                                        id="apply-occupation"
                                        v-model="form.occupation"
                                        type="text"
                                        class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                        :aria-invalid="Boolean(form.errors.occupation)"
                                    />
                                    <p v-if="form.errors.occupation" class="text-sm text-red-700">{{ form.errors.occupation }}</p>
                                </div>
                                <div class="w-full space-y-2">
                                    <label for="apply-employer-name" class="text-sm font-medium text-slate-800">
                                        Nama majikan
                                        <span class="text-xs font-normal text-slate-400">(Pilihan)</span>
                                    </label>
                                    <input
                                        id="apply-employer-name"
                                        v-model="form.employer_name"
                                        type="text"
                                        class="h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                        :aria-invalid="Boolean(form.errors.employer_name)"
                                    />
                                    <p v-if="form.errors.employer_name" class="text-sm text-red-700">{{ form.errors.employer_name }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <div class="space-y-2">
                                        <label for="apply-notes" class="text-sm font-medium text-slate-800">
                                            Catatan
                                            <span class="text-xs font-normal text-slate-400">(Pilihan)</span>
                                        </label>
                                        <textarea
                                            id="apply-notes"
                                            v-model="form.notes"
                                            :rows="4"
                                            maxlength="2000"
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
                                            :aria-invalid="Boolean(form.errors.notes)"
                                        />
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs leading-5 text-slate-500">Nyatakan tujuan permohonan atau maklumat tambahan jika perlu.</p>
                                            <span class="text-[11px] text-slate-400" :class="form.notes.length > 1800 ? 'text-amber-600' : ''">{{ (form.notes || '').length }}/2000</span>
                                        </div>
                                        <p v-if="form.errors.notes" class="text-sm text-red-700">{{ form.errors.notes }}</p>
                                    </div>
                                </div>
                            </FormSection>
                        </div>

                        <!-- STEP 3: Semakan & Tandatangan -->
                        <div v-show="currentStep === 3">
                            <!-- Review Summary -->
                            <FormSection title="Semakan Maklumat" description="Sila semak semua maklumat sebelum menghantar permohonan." :columns="1">
                                <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 bg-slate-50/50">
                                    <div v-for="field in reviewFields" :key="field.label" class="flex items-center justify-between px-4 py-3">
                                        <span class="text-sm text-slate-600">{{ field.label }}</span>
                                        <span class="text-sm font-medium text-slate-900" :class="{ 'text-slate-400': !field.value }">{{ field.value || '-' }}</span>
                                    </div>
                                </div>
                            </FormSection>

                            <FormSection title="Diperkenalkan Oleh" description="Pilih ahli yang memperkenalkan anda ke koperasi. Biarkan kosong jika tiada." :columns="1" class="mt-6">
                                <ReferrerSearchInput
                                    v-model="form.referred_by_member_id"
                                    :error="form.errors.referred_by_member_id"
                                />
                            </FormSection>

                            <FormSection title="Tandatangan Digital" description="Sila tandatangan di dalam kotak di bawah sebagai pengesahan permohonan." :columns="1" class="mt-6">
                                <SignaturePad
                                    v-model="form.digital_signature"
                                    label="Tandatangan"
                                    :error="form.errors.digital_signature"
                                    :disabled="form.processing"
                                />
                                <p class="text-xs text-slate-400 mt-1">Tandatangan diperlukan untuk mengesahkan permohonan anda.</p>
                            </FormSection>
                        </div>

                        <!-- Form Actions -->
                        <div class="sticky bottom-4 z-30 rounded-3xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur-md sm:static sm:shadow-sm sm:backdrop-blur-none sm:bg-white">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <button
                                        v-if="currentStep > 1"
                                        type="button"
                                        class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-100"
                                        @click="goToStep(currentStep - 1)"
                                    >
                                        <ArrowLeft class="h-4 w-4" />
                                        Sebelumnya
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-100"
                                        @click="handleCancel"
                                    >
                                        Batal
                                    </button>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-slate-400 hidden sm:block">Langkah {{ currentStep }} dari {{ totalSteps }}</span>
                                    <button
                                        v-if="currentStep < totalSteps"
                                        type="submit"
                                        class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-teal-800 sm:w-auto"
                                        :disabled="currentStep === 1 && !isStep1Valid"
                                    >
                                        Seterusnya
                                        <ArrowRight class="h-4 w-4" />
                                    </button>
                                    <button
                                        v-else
                                        type="submit"
                                        class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-teal-800 disabled:pointer-events-none disabled:opacity-50 sm:w-auto"
                                        :disabled="form.processing"
                                    >
                                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                                        {{ form.processing ? 'Menghantar...' : 'Hantar Permohonan' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Sidebar Info Cards -->
                    <div class="space-y-6">
                        <div class="rounded-3xl border border-emerald-100 bg-white/90 p-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                    <Users class="h-5 w-5" />
                                </span>
                                <div class="space-y-2">
                                    <h2 class="text-lg font-semibold text-slate-950">Proses ringkas dan jelas</h2>
                                    <p class="text-sm leading-6 text-slate-600">
                                        Permohonan anda akan direkodkan dengan nombor rujukan automatik untuk semakan pihak koperasi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-blue-100 bg-white/90 p-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                                    <ShieldCheck class="h-5 w-5" />
                                </span>
                                <div class="space-y-2">
                                    <h2 class="text-lg font-semibold text-slate-950">Data disemak secara terkawal</h2>
                                    <p class="text-sm leading-6 text-slate-600">
                                        Maklumat yang dihantar hanya digunakan untuk tujuan penilaian permohonan keahlian dan tidak dipaparkan secara awam.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-amber-100 bg-white/90 p-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                                    <UserCheck class="h-5 w-5" />
                                </span>
                                <div class="space-y-2">
                                    <h2 class="text-lg font-semibold text-slate-950">Satu IC, satu permohonan</h2>
                                    <p class="text-sm leading-6 text-slate-600">
                                        Setiap nombor kad pengenalan hanya boleh menghantar satu permohonan yang aktif pada satu-satu masa. Pastikan maklumat anda tepat.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Confirm Cancel Dialog -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showConfirmCancel" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-sm p-4" @click="showConfirmCancel = false">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showConfirmCancel" class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl" @click.stop>
                            <h3 class="text-lg font-semibold text-slate-950">Batalkan Permohonan?</h3>
                            <p class="mt-2 text-sm text-slate-600">Semua maklumat yang telah diisi akan dipadamkan. Tindakan ini tidak boleh dikembalikan.</p>
                            <div class="mt-5 flex gap-3 justify-end">
                                <Button type="button" variant="outline" @click="showConfirmCancel = false">Teruskan Isi</Button>
                                <Button type="button" variant="destructive" @click="confirmCancel">Ya, Batalkan</Button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </PublicLayout>
</template>
