<script setup>
import { ref } from 'vue';
import { Fingerprint, ScanFace } from 'lucide-vue-next';

const showNotice = ref(false);

const previewBiometric = () => {
    showNotice.value = true;
};
</script>

<template>
    <div>
        <button
            type="button"
            class="group flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-700/20"
            aria-describedby="biometric-notice"
            @click="previewBiometric"
        >
            <span class="relative flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white transition group-hover:scale-105">
                <ScanFace class="h-5 w-5" />
                <Fingerprint class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full bg-white p-0.5 text-slate-900" />
            </span>
            <span>Face ID / Touch ID</span>
            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Akan datang</span>
        </button>

        <p
            v-if="showNotice"
            id="biometric-notice"
            class="mt-2 text-center text-xs leading-5 text-slate-500"
            role="status"
        >
            Log masuk biometrik akan tersedia melalui aplikasi mudah alih.
        </p>
    </div>
</template>
