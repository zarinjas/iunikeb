<script setup>
import QRCode from 'qrcode';
import { computed, ref, watch } from 'vue';
import ProfileAvatar from '@/Shared/Components/ProfileAvatar.vue';

const props = defineProps({
    cooperative: {
        type: Object,
        required: true,
    },
    card: {
        type: Object,
        required: true,
    },
    size: {
        type: String,
        default: 'default',
    },
    fluidOnMobile: {
        type: Boolean,
        default: false,
    },
});

const qrCodeDataUrl = ref(null);

const sizeMap = {
    compact: {
        shell: 'max-w-[280px] aspect-[1/1.58] p-5',
        name: 'text-[1.5rem]',
        meta: 'text-[9px]',
        value: 'text-sm',
        qr: 'h-14 w-14',
        avatar: 'lg',
        ring: 'p-1 border-[3px]',
        logo: 'h-8 w-8',
        tng: 'scale-75 origin-bottom-right'
    },
    default: {
        shell: 'max-w-[340px] aspect-[1/1.58] p-6',
        name: 'text-[1.75rem]',
        meta: 'text-[10px]',
        value: 'text-lg',
        qr: 'h-16 w-16',
        avatar: 'xl',
        ring: 'p-1.5 border-[3px]',
        logo: 'h-10 w-10',
        tng: 'scale-90 origin-bottom-right'
    },
    large: {
        shell: 'max-w-[400px] aspect-[1/1.58] p-8',
        name: 'text-[2.2rem]',
        meta: 'text-xs',
        value: 'text-xl',
        qr: 'h-20 w-20',
        avatar: 'xl',
        ring: 'p-2 border-[4px]',
        logo: 'h-12 w-12',
        tng: 'scale-100 origin-bottom-right'
    },
};

const currentSize = computed(() => sizeMap[props.size] ?? sizeMap.default);

const shellClasses = computed(() => [
    'relative isolate overflow-hidden rounded-[1.5rem] border border-white/20 bg-gradient-to-br from-[#003B73] via-[#005B8F] to-[#00A499] text-white shadow-xl shadow-cyan-950/20 flex flex-col',
    currentSize.value.shell,
    props.fluidOnMobile ? 'max-w-none sm:max-w-[340px]' : '',
    props.card.readiness?.is_active ? '' : 'grayscale-[0.2]',
]);

const generateQrCode = async () => {
    qrCodeDataUrl.value = await QRCode.toDataURL(props.card.verification_url, {
        errorCorrectionLevel: 'H',
        margin: 1,
        width: 420,
        color: {
            dark: '#003B73',
            light: '#ffffff',
        },
    });
};

watch(() => props.card.verification_url, () => {
    generateQrCode();
}, { immediate: true });
</script>

<template>
    <article class="relative w-full">
        <div :class="shellClasses">
            <!-- Background overlays -->
            <!-- Subtle noise or radial gradient -->
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.1),_transparent_40%),radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.1),_transparent_50%)]" />
            
            <div class="relative z-10 flex h-full flex-col">
                <!-- Top Section -->
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center justify-center rounded-lg bg-white" :class="currentSize.logo">
                            <img
                                v-if="cooperative.logo_url"
                                :src="cooperative.logo_url"
                                :alt="cooperative.name"
                                class="h-full w-full object-contain p-1"
                            />
                            <span v-else class="font-bold text-slate-800 text-sm">KH</span>
                        </div>
                        <div class="flex flex-col pt-0.5 max-w-[150px]">
                            <h1 class="text-[11px] font-bold uppercase leading-[1.2] tracking-wider text-white">
                                {{ cooperative.name }}
                            </h1>
                            <p class="mt-1.5 text-[8px] font-medium text-white/90">Bersama Membina Masa Depan</p>
                        </div>
                    </div>
                    
                    <!-- NFC Icon -->
                    <div class="flex flex-col items-center pt-1">
                        <!-- NFC Waves Icon (using a rotated wifi/radio icon) -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7 text-white/90 rotate-90"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                        <span class="mt-1 text-[9px] font-bold tracking-widest text-white/90 uppercase">NFC</span>
                    </div>
                </div>

                <div class="flex-grow"></div>

                <!-- Middle Section -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex flex-col gap-1">
                        <p class="text-[9px] font-semibold uppercase tracking-widest text-[#66d9cc]">Kad Keahlian</p>
                        <p class="text-[9px] font-semibold uppercase tracking-widest text-[#66d9cc]">Digital & Touch 'N Go</p>
                    </div>
                    
                    <!-- Avatar with ring -->
                    <div class="rounded-full border-teal-500/40 bg-teal-600/20 backdrop-blur-sm" :class="currentSize.ring">
                        <ProfileAvatar
                            :photo-url="card.profile_photo_url"
                            :name="card.full_name"
                            :size="currentSize.avatar"
                            class="border-4 border-teal-300 shadow-md"
                        />
                    </div>
                </div>

                <!-- Name Section -->
                <div class="mb-8">
                    <h2 class="font-bold tracking-tight text-white drop-shadow-sm leading-tight" :class="currentSize.name">
                        {{ card.full_name }}
                    </h2>
                </div>

                <!-- Bottom Section -->
                <div class="flex items-end justify-between">
                    <div class="flex flex-col gap-5">
                        <div>
                            <p class="font-semibold uppercase tracking-widest text-[#66d9cc]" :class="currentSize.meta">No. Ahli</p>
                            <p class="mt-0.5 font-bold text-white drop-shadow-sm" :class="currentSize.value">{{ card.member_no }}</p>
                        </div>
                        <div>
                            <p class="font-semibold uppercase tracking-widest text-[#66d9cc]" :class="currentSize.meta">Sah Sehingga</p>
                            <p class="mt-0.5 font-bold text-white drop-shadow-sm" :class="currentSize.value">{{ card.valid_until || 'AKTIF' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <!-- QR Code (Smaller, functional) -->
                        <div class="rounded-lg bg-white p-1 shadow-md">
                            <img
                                v-if="qrCodeDataUrl"
                                :src="qrCodeDataUrl"
                                alt="QR"
                                :class="currentSize.qr"
                            />
                        </div>

                        <!-- Touch 'n Go eWallet Logo Replica -->
                        <div class="flex flex-col items-center justify-center rounded-[0.8rem] bg-[#004f98] px-2.5 py-1.5 shadow-lg border-[2px] border-white" :class="currentSize.tng">
                            <div class="text-[13px] font-bold italic leading-none text-white tracking-tight">Touch</div>
                            <div class="text-[14px] font-bold italic leading-none text-white tracking-tight">'n Go</div>
                            <div class="mt-1 w-full rounded bg-[#f3c200] px-1 py-0.5 text-center text-[8px] font-black uppercase text-[#004f98]">eWallet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>
