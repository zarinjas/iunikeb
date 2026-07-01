<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowUpRight, Calculator, CalendarDays, ChevronRight, CircleAlert, CircleCheck, CheckCircle2, Clock, Eye, EyeOff, FileCheck, FileText, Gift, HandCoins, ImagePlay, Mail, MapPin, Megaphone, MessagesSquare, Phone, Pin, ScrollText, ShoppingBag, UserRound, Vote, Wallet, X, Zap } from 'lucide-vue-next';
import { computed, ref, onMounted } from 'vue';
import QRCode from 'qrcode';
import MemberLayout from '@/Member/Layouts/MemberLayout.vue';
import EmptyState from '@/Shared/Components/EmptyState.vue';
import BannerCarousel from '@/Shared/Components/BannerCarousel.vue';
import PosterCarousel from '@/Shared/Components/PosterCarousel.vue';
import ProductSlider from '@/Shared/Components/ProductSlider.vue';
import QrScannerModal from '@/Shared/Components/QrScannerModal.vue';
import ProgramCarousel from '@/Shared/Components/ProgramCarousel.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import PwaInstallBanner from '@/Shared/Components/PwaInstallBanner.vue';
import Dialog from '@/Shared/Components/ui/dialog/Dialog.vue';
import MemberDigitalCard from '@/Shared/Components/MemberDigitalCard.vue';

const props = defineProps({
    member: { type: Object, required: true },
    onboardingCompleted: { type: Boolean, default: false },
    profileCompletionPercent: { type: Number, default: 0 },
    missingFields: { type: Array, default: () => [] },
    digitalCard: { type: Object, default: null },
    application: { type: Object, default: null },
    quickActions: { type: Array, required: true },
    featuredForms: { type: Array, required: true },
    recentSubmissions: { type: Array, default: () => [] },
    latestAnnouncements: { type: Array, required: true },
    financingSummary: { type: Object, default: null },
    ansuranProducts: { type: Array, default: () => [] },
    caruman: { type: Object, default: null },
    posters: { type: Array, default: () => [] },
    banners: { type: Array, default: () => [] },
    upcomingPrograms: { type: Array, default: () => [] },
    activeSurveys: { type: Array, default: () => [] },
});

const page = usePage();
const contact = computed(() => page.props.appSettings?.contact ?? {});
const coop = computed(() => page.props.appSettings?.cooperative ?? {});

const showCardDialog = ref(false);
const showCaruman = ref(false);
const activeCarumanTab = ref('semasa');

const carumanTabs = [
    { key: 'semasa', label: 'Semasa' },
    { key: 'keseluruhan', label: 'Keseluruhan' },
    { key: 'dividen', label: 'Dividen' },
];

const toggleCaruman = () => { showCaruman.value = !showCaruman.value; };

const formatCaruman = (value) => {
    if (value === null || value === undefined) return '*****';
    if (!showCaruman.value) return 'RM *****';
    return 'RM ' + Number(value).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const activeCarumanValue = computed(() => {
    const tab = activeCarumanTab.value;
    if (tab === 'semasa') return props.caruman?.caruman_semasa ?? 0;
    if (tab === 'keseluruhan') return props.caruman?.caruman_keseluruhan ?? 0;
    if (tab === 'dividen') return props.caruman?.dividen ?? 0;
    return 0;
});

const activeCarumanLabel = computed(() => {
    const tab = activeCarumanTab.value;
    if (tab === 'semasa') return 'Caruman Setakat Ini';
    if (tab === 'keseluruhan') return 'Caruman Keseluruhan';
    if (tab === 'dividen') return 'Dividen Tahun Ini';
    return '';
});

const isCarumanDividen = computed(() => activeCarumanTab.value === 'dividen');

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat pagi';
    if (hour < 15) return 'Selamat tengah hari';
    if (hour < 19) return 'Selamat petang';
    return 'Selamat malam';
});

const greetingEmoji = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return '\u{1F305}';
    if (hour < 15) return '\u{2600}\u{FE0F}';
    if (hour < 19) return '\u{1F324}';
    return '\u{1F319}';
});

const hasFinancing = computed(() => {
    return props.financingSummary && (props.financingSummary.under_review > 0 || props.financingSummary.guarantor_requests > 0);
});

const actionIcon = (name) => {
    const map = { FileCheck, HandCoins, MessagesSquare, UserRound };
    return map[name] ?? UserRound;
};

const showReferral = ref(true);
const closeReferral = () => { showReferral.value = false; };

const showOnboardingBanner = ref(true);
const closeOnboardingBanner = () => { showOnboardingBanner.value = false; };

const scannerOpen = ref(false);
const activeFormTab = ref('borang');

const selectedOptionId = ref({});
const voting = ref({});

const voteFromDashboard = (surveyId) => {
    const optionId = selectedOptionId.value[surveyId];
    if (!optionId) return;
    voting.value[surveyId] = true;
    router.post(`/member/surveys/${surveyId}/vote`, {
        survey_option_id: optionId,
    }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            voting.value[surveyId] = false;
        },
    });
};

const handleQrScan = (decodedText) => {
    const programMatch = decodedText.match(/\/member\/programs\/(\d+)/);
    if (programMatch) {
        const programId = programMatch[1];
        router.post(`/member/programs/${programId}/check-in`, {}, {
            preserveScroll: false,
            onFinish: () => {
                scannerOpen.value = false;
            },
        });
    } else {
        scannerOpen.value = false;
    }
};

const qrCodeDataUrl = ref(null);

onMounted(async () => {
    if (props.digitalCard?.verification_url) {
        try {
            qrCodeDataUrl.value = await QRCode.toDataURL(props.digitalCard.verification_url, {
                margin: 1,
                width: 200,
                color: { dark: '#0f766e', light: '#ffffff' },
            });
        } catch {
            qrCodeDataUrl.value = null;
        }
    }
});
</script>

<template>
    <Head title="Dashboard Ahli" />

    <MemberLayout>
        <div class="space-y-3 pb-28">
            <!-- Referral Engagement (top banner, dismissible) -->
            <div v-if="showReferral">
                <Link
                    href="/member/referrals"
                    class="relative flex items-center gap-3 rounded-2xl bg-gradient-to-r from-rose-50 via-rose-50 to-orange-50 px-4 py-3.5 shadow-sm ring-1 ring-rose-200/60 transition hover:shadow-md"
                >
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-500">
                        <Gift class="h-[18px] w-[18px]" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-rose-700">Rujuk Rakan & Dapatkan Ganjaran</p>
                        <p class="mt-0.5 text-xs text-rose-500">Jemput rakan sertai koperasi, nikmati ganjaran istimewa!</p>
                    </div>
                    <ChevronRight class="h-4 w-4 shrink-0 text-rose-400" />
                    <button
                        type="button"
                        class="absolute -right-1 -top-1 flex h-6 w-6 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition hover:text-slate-600"
                        @click.prevent="closeReferral"
                    >
                        <X class="h-3 w-3" />
                    </button>
                </Link>
            </div>

            <!-- Banner Carousel -->
            <section v-if="banners.length">
                <BannerCarousel :banners="banners" />
            </section>

            <!-- Unlinked Warning -->
            <div v-if="!member.is_linked" class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
                <CircleAlert class="h-5 w-5 shrink-0" />
                Rekod ahli anda belum dipautkan sepenuhnya. Sesetengah maklumat portal mungkin belum tersedia.
            </div>

            <!-- Onboarding Banner -->
            <div v-if="member.is_linked && !onboardingCompleted && showOnboardingBanner" class="relative overflow-hidden rounded-2xl border border-sky-200 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-4 shadow-sm">
                <button
                    type="button"
                    class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full text-sky-400 transition hover:bg-sky-100 hover:text-sky-600"
                    @click="closeOnboardingBanner"
                >
                    <X class="h-3.5 w-3.5" />
                </button>
                <div class="flex items-start gap-3 pr-6">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                        <UserRound class="h-5 w-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-sky-900">Lengkapkan Profil Anda</p>
                        <p class="mt-0.5 text-xs leading-5 text-sky-600">
                            Lengkapkan maklumat berikut untuk memudahkan urusan koperasi:
                        </p>
                        <ul class="mt-2 space-y-1">
                            <li v-for="field in missingFields" :key="field" class="flex items-center gap-1.5 text-xs text-sky-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-400" />
                                {{ field }}
                            </li>
                        </ul>
                        <div class="mt-3 flex items-center gap-3">
                            <div class="flex-1">
                                <div class="h-1.5 overflow-hidden rounded-full bg-sky-200">
                                    <div
                                        class="h-full rounded-full bg-sky-500 transition-all duration-500"
                                        :style="{ width: profileCompletionPercent + '%' }"
                                    />
                                </div>
                                <p class="mt-0.5 text-[11px] text-sky-500">Profil {{ profileCompletionPercent }}% lengkap</p>
                            </div>
                            <Link
                                href="/member/profile?edit=1"
                                class="shrink-0 rounded-lg bg-sky-600 px-3.5 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-sky-700"
                            >
                                Kemaskini
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero / Member Card -->
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 px-6 py-6 shadow-xl ring-1 ring-slate-700/50 sm:px-7 sm:py-7">
                <!-- Ambient Glows -->
                <div class="pointer-events-none absolute -bottom-24 -right-24 h-64 w-64 rounded-full bg-amber-500/15 blur-[60px]"></div>
                <div class="pointer-events-none absolute -left-20 -top-20 h-56 w-56 rounded-full bg-sky-500/15 blur-[50px]"></div>
                
                <!-- Premium Glass Shimmer / Light Reflection -->
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-white/5 via-transparent to-white/5 mix-blend-overlay"></div>
                <div class="pointer-events-none absolute -inset-x-full top-0 h-[250%] w-[300%] -rotate-[35deg] transform bg-gradient-to-b from-transparent via-white/5 to-transparent"></div>

                <div class="relative z-10 flex flex-col gap-6">
                    <!-- Top Header -->
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold tracking-[0.25em] text-slate-400 uppercase drop-shadow-sm">
                                Kad Keahlian
                            </p>
                            <p class="text-xs font-medium text-slate-300">
                                {{ greeting }} <span class="text-[11px]">{{ greetingEmoji }}</span>
                            </p>
                        </div>
                        
                        <!-- QR Code Top Right -->
                        <div v-if="digitalCard && qrCodeDataUrl" class="shrink-0">
                            <div class="h-12 w-12 sm:h-14 sm:w-14 overflow-hidden rounded-xl bg-white/10 p-1.5 backdrop-blur-md ring-1 ring-white/20 shadow-md">
                                <img :src="qrCodeDataUrl" alt="QR Kod Ahli" class="h-full w-full rounded-md bg-white" />
                            </div>
                        </div>
                    </div>

                    <!-- Main Profile Row -->
                    <div class="mt-1 flex items-center gap-4 sm:gap-6">
                        <!-- Huge Avatar -->
                        <div v-if="digitalCard" class="relative shrink-0">
                            <div class="h-20 w-20 sm:h-24 sm:w-24 overflow-hidden rounded-full bg-slate-800 ring-4 ring-slate-700/80 shadow-2xl">
                                <img
                                    v-if="digitalCard.profile_photo_url"
                                    :src="digitalCard.profile_photo_url"
                                    :alt="member.full_name"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-3xl font-bold text-slate-300"
                                >
                                    {{ (member.full_name?.[0] || '?')?.toUpperCase() }}
                                </div>
                            </div>
                            <!-- Status Badge overlapping Avatar -->
                            <span
                                class="absolute -bottom-2 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider backdrop-blur-md ring-1 shadow-lg"
                                :class="member.membership_status === 'active' ? 'bg-emerald-500/90 text-white ring-emerald-400/50' : 'bg-amber-500/90 text-white ring-amber-400/50'"
                            >
                                {{ member.membership_status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                        
                        <!-- Details -->
                        <div class="min-w-0 flex-1 py-1">
                            <h1 class="text-xl font-bold tracking-tight text-slate-50 drop-shadow-md sm:text-2xl leading-tight">
                                {{ member.full_name }}
                            </h1>
                            <div class="mt-2 font-mono text-base font-medium tracking-[0.15em] text-slate-300 drop-shadow-md sm:text-lg">
                                {{ member.member_no || 'SEMENTARA' }}
                            </div>
                            <div v-if="member.joined_at" class="mt-1 flex items-center text-[10px] uppercase tracking-[0.1em] text-slate-400">
                                Ahli sejak {{ member.joined_at }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Click to view full card -->
            <p v-if="digitalCard" class="-mt-2 text-center">
                <button
                    class="text-xs font-medium text-teal-600 underline underline-offset-2 transition-colors hover:text-teal-700"
                    @click="showCardDialog = true"
                >
                    Klik untuk lihat kad penuh
                </button>
            </p>

            <!-- Full card popup dialog -->
            <Dialog v-model:open="showCardDialog" class="border-none bg-transparent p-0 shadow-none w-auto flex justify-center items-center">
                <MemberDigitalCard
                    :cooperative="$page.props.appSettings.cooperative"
                    :card="digitalCard"
                    size="large"
                />
            </Dialog>

            <!-- Account Summary / Caruman — banking style -->
            <section v-if="caruman" class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-50 via-white to-emerald-50 shadow-sm ring-1 ring-teal-100/30">
                <div class="px-5 pb-4 pt-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-teal-400 to-emerald-500 text-white shadow-sm">
                                <Wallet class="h-[18px] w-[18px]" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Ringkasan Akaun</p>
                                <p class="text-[11px] text-slate-400">Tahun {{ caruman.year }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/70 hover:text-slate-600"
                            :title="showCaruman ? 'Sembunyikan jumlah' : 'Tunjukkan jumlah'"
                            @click="toggleCaruman"
                        >
                            <Eye v-if="showCaruman" class="h-[18px] w-[18px]" />
                            <EyeOff v-else class="h-[18px] w-[18px]" />
                        </button>
                    </div>

                    <div class="mt-1 h-px w-full bg-gradient-to-r from-teal-200/60 via-emerald-200/30 to-transparent" />

                    <!-- Pill Tabs -->
                    <div class="mt-4 flex gap-1.5 rounded-xl bg-white/70 p-1 shadow-xs ring-1 ring-slate-200/50">
                        <button
                            v-for="tab in carumanTabs"
                            :key="tab.key"
                            type="button"
                            class="flex-1 rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200"
                            :class="activeCarumanTab === tab.key ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            @click="activeCarumanTab = tab.key"
                        >
                            {{ tab.label }}
                        </button>
                    </div>

                    <!-- Value Display -->
                    <div class="mt-4">
                        <p class="text-[11px] font-medium text-slate-400">{{ activeCarumanLabel }}</p>
                        <div class="flex items-baseline gap-2">
                            <p
                                class="mt-0.5 text-3xl font-bold tabular-nums tracking-tight"
                                :class="isCarumanDividen ? 'text-emerald-600' : 'text-slate-900'"
                            >
                                {{ formatCaruman(activeCarumanValue) }}
                            </p>
                        </div>
                        <div v-if="showCaruman && caruman && activeCarumanTab === 'semasa'" class="mt-1.5 flex items-center gap-1 text-[11px] text-teal-600">
                            <CircleCheck class="h-3 w-3" />
                            <span>Caruman semasa untuk tahun {{ caruman.year }}</span>
                        </div>
                    </div>

                    <div class="mt-4 h-px w-full bg-gradient-to-r from-teal-200/30 via-emerald-200/20 to-transparent" />

                    <!-- Quick CTAs -->
                    <div class="mt-3 flex gap-2">
                        <Link
                            href="/member/caruman"
                            class="flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 px-3 py-2.5 text-xs font-medium text-white shadow-sm transition hover:shadow-md hover:brightness-105"
                        >
                            <Wallet class="h-3.5 w-3.5" />
                            Butiran
                        </Link>
                        <Link
                            href="/member/caruman"
                            class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-teal-200 bg-white/70 px-3 py-2.5 text-xs font-medium text-teal-700 shadow-sm transition hover:bg-teal-50 hover:shadow-md"
                        >
                            <FileText class="h-3.5 w-3.5" />
                            Penyata
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Status Permohonan (shown when no caruman) -->
            <section v-else-if="digitalCard" class="rounded-2xl bg-gradient-to-br from-amber-50 via-white to-orange-50 shadow-sm ring-1 ring-amber-200/30">
                <div class="p-5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-sm">
                            <ScrollText class="h-[18px] w-[18px]" />
                        </span>
                        <p class="text-sm font-semibold text-slate-900">Status Permohonan</p>
                    </div>
                    <div v-if="application" class="mt-3 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ application.application_no }}</p>
                                <p class="text-xs text-slate-400">{{ application.submitted_at || '-' }}</p>
                            </div>
                            <StatusBadge :status="application.status" />
                        </div>
                        <Link
                            href="/member/applications"
                            class="inline-flex items-center gap-1 text-xs font-medium text-teal-600 hover:text-teal-700"
                        >
                            Semak Permohonan
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                    <div v-else class="mt-3 text-sm text-slate-400">
                        Tiada permohonan keahlian dipautkan pada akaun anda setakat ini.
                    </div>
                </div>
            </section>

            <!-- Quick Actions -->
            <div class="grid grid-cols-4 gap-2">
                <Link
                    v-for="(action, idx) in quickActions"
                    :key="action.href"
                    :href="action.href"
                    class="group relative flex flex-col items-center gap-2 overflow-hidden rounded-2xl bg-white px-1.5 py-4 shadow-sm ring-1 ring-slate-100 transition-all duration-200 active:scale-95"
                >
                    <!-- Subtle background glow -->
                    <span
                        class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                        :class="[
                            idx === 0 ? 'bg-gradient-to-b from-teal-50/80 to-transparent' : '',
                            idx === 1 ? 'bg-gradient-to-b from-blue-50/80 to-transparent' : '',
                            idx === 2 ? 'bg-gradient-to-b from-amber-50/80 to-transparent' : '',
                            idx === 3 ? 'bg-gradient-to-b from-emerald-50/80 to-transparent' : '',
                        ]"
                    />
                    <span
                        class="relative flex h-11 w-11 items-center justify-center rounded-2xl text-white shadow-md transition-transform duration-200 group-hover:scale-105 group-active:scale-95"
                        :class="[
                            idx === 0 ? 'bg-gradient-to-br from-teal-400 to-emerald-500 shadow-teal-200' : '',
                            idx === 1 ? 'bg-gradient-to-br from-blue-400 to-indigo-500 shadow-blue-200' : '',
                            idx === 2 ? 'bg-gradient-to-br from-amber-400 to-orange-500 shadow-amber-200' : '',
                            idx === 3 ? 'bg-gradient-to-br from-emerald-400 to-teal-500 shadow-emerald-200' : '',
                        ]"
                    >
                        <component :is="actionIcon(action.icon)" class="h-[18px] w-[18px]" />
                    </span>
                    <span
                        class="relative text-center text-[11px] font-semibold leading-tight"
                        :class="[
                            idx === 0 ? 'text-teal-700' : '',
                            idx === 1 ? 'text-blue-700' : '',
                            idx === 2 ? 'text-amber-700' : '',
                            idx === 3 ? 'text-emerald-700' : '',
                        ]"
                    >
                        {{ action.label }}
                    </span>
                </Link>
            </div>

            <!-- 1. Pembiayaan -->
            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-sm">
                                <HandCoins class="h-[18px] w-[18px]" />
                            </span>
                            <p class="text-sm font-semibold text-slate-900">Pembiayaan</p>
                        </div>
                        <Link
                            href="/member/financing"
                            class="inline-flex items-center gap-0.5 text-xs font-medium text-teal-600 hover:text-teal-700"
                        >
                            Semua
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>

                    <div v-if="hasFinancing" class="mb-3 flex gap-2.5 overflow-x-auto scrollbar-none pb-0.5">
                        <div v-if="financingSummary.under_review > 0" class="flex shrink-0 items-center gap-3 rounded-xl bg-blue-50 px-4 py-3 ring-1 ring-blue-100/50">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                <FileText class="h-4 w-4" />
                            </span>
                            <div>
                                <p class="text-xs text-blue-500">Dalam Semakan</p>
                                <p class="text-lg font-bold text-slate-900">{{ financingSummary.under_review }}</p>
                            </div>
                        </div>
                        <Link
                            v-if="financingSummary.guarantor_requests > 0"
                            href="/member/financing/guarantor-requests"
                            class="flex shrink-0 items-center gap-3 rounded-xl bg-amber-50 px-4 py-3 ring-1 ring-amber-100/50 transition hover:bg-amber-100 hover:ring-amber-200"
                        >
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                <ArrowUpRight class="h-4 w-4" />
                            </span>
                            <div>
                                <p class="text-xs text-amber-500">Permintaan Penjamin</p>
                                <p class="text-lg font-bold text-slate-900">{{ financingSummary.guarantor_requests }}</p>
                            </div>
                        </Link>
                    </div>

                    <div class="flex gap-2">
                        <Link
                            href="/member/financing"
                            class="flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:shadow-md hover:brightness-105"
                        >
                            <HandCoins class="h-4 w-4" />
                            Mohon Baru
                        </Link>
                        <Link
                            href="/member/financing/calculator"
                            class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-700"
                        >
                            <Calculator class="h-4 w-4" />
                            Anggaran
                        </Link>
                    </div>
                </div>
            </section>

            <!-- 2. Program Akan Datang -->
            <section v-if="upcomingPrograms.length" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-teal-400 to-emerald-500 text-white shadow-sm">
                                <CalendarDays class="h-[18px] w-[18px]" />
                            </span>
                            <p class="text-sm font-semibold text-slate-900">Program Akan Datang</p>
                        </div>
                        <Link
                            href="/member/programs"
                            class="inline-flex items-center gap-0.5 text-xs font-medium text-teal-600 hover:text-teal-700"
                        >
                            Semua
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                    <ProgramCarousel :programs="upcomingPrograms" />
                </div>
            </section>

            <!-- 3. Pengumuman (max 3) -->
            <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                                <Megaphone class="h-[18px] w-[18px]" />
                            </span>
                            <p class="text-sm font-semibold text-slate-900">Pengumuman</p>
                        </div>
                        <Link
                            v-if="latestAnnouncements.length"
                            href="/member/announcements"
                            class="inline-flex items-center gap-0.5 text-xs font-medium text-teal-600 hover:text-teal-700"
                        >
                            Semua
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>

                    <div v-if="latestAnnouncements.length" class="space-y-1.5">
                        <div
                            v-for="item in latestAnnouncements.slice(0, 3)"
                            :key="item.id"
                        >
                            <Link
                                :href="item.show_url"
                                class="flex gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-slate-50"
                                :class="{
                                    'bg-amber-50/40': item.priority === 'penting',
                                    'bg-red-50/40': item.priority === 'segera',
                                }"
                            >
                                <span
                                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                                    :class="{
                                        'bg-teal-50 text-teal-500': item.priority === 'normal',
                                        'bg-amber-100 text-amber-600': item.priority === 'penting',
                                        'bg-red-100 text-red-600': item.priority === 'segera',
                                    }"
                                >
                                    <Zap v-if="item.priority === 'segera' || item.priority === 'penting'" class="h-4 w-4" />
                                    <Megaphone v-else class="h-4 w-4" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-slate-900">{{ item.title }}</p>
                                        <span
                                            v-if="item.priority === 'segera'"
                                            class="shrink-0 rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-600"
                                        >Segera</span>
                                        <span
                                            v-else-if="item.priority === 'penting'"
                                            class="shrink-0 rounded-full bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-600"
                                        >Penting</span>
                                        <Pin v-if="item.is_pinned" class="h-3 w-3 shrink-0 text-amber-400" />
                                    </div>
                                    <p v-if="item.summary" class="mt-0.5 line-clamp-1 text-xs text-slate-400">{{ item.summary }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ item.published_at || '-' }}</p>
                                </div>
                            </Link>
                        </div>
                        <Link
                            v-if="latestAnnouncements.length > 3"
                            href="/member/announcements"
                            class="flex items-center justify-center gap-1 rounded-xl border border-slate-100 py-2.5 text-xs font-medium text-teal-600 transition hover:bg-teal-50"
                        >
                            Lihat Semua Pengumuman
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                    <EmptyState
                        v-else
                        class="mt-3"
                        title="Tiada pengumuman baharu"
                        description="Pengumuman terkini akan dipaparkan di sini."
                        compact
                    />
                </div>
            </section>

            <!-- 3. Poster & Infografik -->
            <section v-if="posters.length" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 to-fuchsia-500 text-white shadow-sm">
                                <ImagePlay class="h-[18px] w-[18px]" />
                            </span>
                            <p class="text-sm font-semibold text-slate-900">Poster & Infografik</p>
                        </div>
                        <Link
                            href="/member/posters"
                            class="inline-flex items-center gap-0.5 text-xs font-medium text-teal-600 hover:text-teal-700"
                        >
                            Semua
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                    <PosterCarousel :posters="posters" />
                </div>
            </section>


            <!-- 5. Ansuran Mudah -->
            <section v-if="ansuranProducts.length" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-cyan-500 text-white shadow-sm">
                                <ShoppingBag class="h-[18px] w-[18px]" />
                            </span>
                            <p class="text-sm font-semibold text-slate-900">Ansuran Mudah</p>
                        </div>
                        <Link
                            href="/member/ansuran"
                            class="inline-flex items-center gap-0.5 text-xs font-medium text-teal-600 hover:text-teal-700"
                        >
                            Semua
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                    <ProductSlider :products="ansuranProducts" />
                </div>
            </section>

            <!-- 6. Borang & Permohonan (merged with tabs) -->
            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-sm">
                            <FileCheck class="h-[18px] w-[18px]" />
                        </span>
                        <p class="text-sm font-semibold text-slate-900">Borang & Permohonan</p>
                    </div>

                    <!-- Pill tabs -->
                    <div class="mb-3 flex gap-1.5 rounded-xl bg-slate-100/70 p-1">
                        <button
                            type="button"
                            class="flex-1 rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200"
                            :class="activeFormTab === 'borang' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            @click="activeFormTab = 'borang'"
                        >
                            Borang Tersedia
                        </button>
                        <button
                            type="button"
                            class="flex-1 rounded-lg px-3 py-2 text-xs font-medium transition-all duration-200"
                            :class="activeFormTab === 'permohonan' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            @click="activeFormTab = 'permohonan'"
                        >
                            Permohonan Saya
                        </button>
                    </div>

                    <!-- Borang tab -->
                    <div v-if="activeFormTab === 'borang'">
                        <div v-if="featuredForms.length" class="space-y-1.5">
                            <Link
                                v-for="form in featuredForms.slice(0, 2)"
                                :key="form.id"
                                :href="form.url"
                                class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-2.5 transition hover:border-emerald-200 hover:bg-emerald-50/30"
                            >
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                                    <FileText class="h-4 w-4" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-slate-900">{{ form.title }}</p>
                                    <p class="text-xs text-slate-400">{{ form.category_name || 'Borang' }}</p>
                                </div>
                                <ChevronRight class="h-4 w-4 shrink-0 text-slate-300" />
                            </Link>
                            <Link
                                href="/member/forms"
                                class="flex items-center justify-center gap-1 rounded-xl border border-slate-100 py-2.5 text-xs font-medium text-teal-600 transition hover:bg-teal-50"
                            >
                                Lihat Semua Borang
                                <ChevronRight class="h-3.5 w-3.5" />
                            </Link>
                        </div>
                        <EmptyState
                            v-else
                            class="mt-3"
                            title="Tiada borang tersedia"
                            description="Borang yang diterbitkan akan dipaparkan di sini."
                            compact
                        />
                    </div>

                    <!-- Permohonan tab -->
                    <div v-if="activeFormTab === 'permohonan'">
                        <div v-if="recentSubmissions.length" class="space-y-1.5">
                            <Link
                                v-for="sub in recentSubmissions.slice(0, 2)"
                                :key="sub.id"
                                :href="sub.detail_url"
                                class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-2.5 transition hover:border-violet-200 hover:bg-violet-50/30"
                            >
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-500">
                                    <FileText class="h-4 w-4" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-slate-900">{{ sub.form_title }}</p>
                                    <p class="text-xs text-slate-400">{{ sub.reference_no }} · {{ sub.submitted_at }}</p>
                                </div>
                                <StatusBadge :status="sub.status" :label="sub.status_label || sub.status" />
                            </Link>
                            <Link
                                href="/member/applications"
                                class="flex items-center justify-center gap-1 rounded-xl border border-slate-100 py-2.5 text-xs font-medium text-teal-600 transition hover:bg-teal-50"
                            >
                                Lihat Semua Permohonan
                                <ChevronRight class="h-3.5 w-3.5" />
                            </Link>
                        </div>
                        <EmptyState
                            v-else
                            class="mt-3"
                            title="Tiada permohonan"
                            description="Permohonan borang yang telah dihantar akan dipaparkan di sini."
                            compact
                        />
                    </div>
                </div>
            </section>

            <!-- 7. Undian Aktif — only shown when data exists, max 1 -->
            <section v-if="activeSurveys.length" class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-sm">
                                <Vote class="h-[18px] w-[18px]" />
                            </span>
                            <p class="text-sm font-semibold text-slate-900">Undian Aktif</p>
                        </div>
                        <Link
                            v-if="activeSurveys.length > 1"
                            href="/member/surveys"
                            class="inline-flex items-center gap-0.5 text-xs font-medium text-teal-600 hover:text-teal-700"
                        >
                            Semua
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                        <div class="flex items-start gap-3">
                            <span
                                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                                :class="activeSurveys[0].has_voted ? 'bg-emerald-50 text-emerald-500' : 'bg-violet-50 text-violet-500'"
                            >
                                <component :is="activeSurveys[0].has_voted ? CheckCircle2 : Vote" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-900">{{ activeSurveys[0].question }}</p>
                                <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                    <span>{{ activeSurveys[0].total_responses }} undian</span>
                                    <span v-if="activeSurveys[0].expires_at">· Tamat {{ activeSurveys[0].expires_at }}</span>
                                </div>

                                <div v-if="!activeSurveys[0].has_voted" class="mt-2 space-y-1.5">
                                    <label
                                        v-for="option in activeSurveys[0].options"
                                        :key="option.id"
                                        class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-xs transition"
                                        :class="selectedOptionId[activeSurveys[0].id] === option.id ? 'border-violet-400 bg-violet-50' : 'border-slate-200 hover:border-slate-300'"
                                    >
                                        <input
                                            type="radio"
                                            :name="'survey_' + activeSurveys[0].id"
                                            :value="option.id"
                                            class="h-3.5 w-3.5 border-slate-300 text-violet-600 focus:ring-violet-200"
                                            @change="selectedOptionId[activeSurveys[0].id] = option.id"
                                        />
                                        {{ option.label }}
                                    </label>
                                    <button
                                        type="button"
                                        class="mt-2 w-full rounded-lg bg-gradient-to-r from-violet-500 to-purple-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:brightness-105 disabled:opacity-50"
                                        :disabled="!selectedOptionId[activeSurveys[0].id] || voting[activeSurveys[0].id]"
                                        @click="voteFromDashboard(activeSurveys[0].id)"
                                    >
                                        {{ voting[activeSurveys[0].id] ? 'Menghantar...' : 'Hantar Undian' }}
                                    </button>
                                </div>
                                <div v-else class="mt-2 flex items-center gap-1.5 text-xs text-emerald-600">
                                    <CheckCircle2 class="h-3.5 w-3.5" />
                                    Anda telah mengundi
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. Hubungi Koperasi — compact horizontal icon row -->
            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="px-4 py-3.5">
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white shadow-sm">
                            <Phone class="h-[18px] w-[18px]" />
                        </span>
                        <p class="text-sm font-semibold text-slate-900">Hubungi Koperasi</p>
                    </div>

                    <div class="grid grid-cols-4 gap-2">
                        <a
                            v-if="contact.phone"
                            :href="`tel:${contact.phone}`"
                            class="flex flex-col items-center gap-1.5 rounded-xl bg-sky-50 px-2 py-3 transition hover:bg-sky-100"
                        >
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-sky-600 shadow-sm ring-1 ring-sky-100">
                                <Phone class="h-4 w-4" />
                            </span>
                            <span class="text-xs font-medium text-sky-700">Telefon</span>
                        </a>

                        <a
                            v-if="contact.whatsapp"
                            :href="`https://wa.me/${contact.whatsapp.replace(/[^0-9]/g, '')}`"
                            target="_blank"
                            class="flex flex-col items-center gap-1.5 rounded-xl bg-emerald-50 px-2 py-3 transition hover:bg-emerald-100"
                        >
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm ring-1 ring-emerald-100">
                                <MessagesSquare class="h-4 w-4" />
                            </span>
                            <span class="text-xs font-medium text-emerald-700">WhatsApp</span>
                        </a>

                        <a
                            v-if="contact.email"
                            :href="`mailto:${contact.email}`"
                            class="flex flex-col items-center gap-1.5 rounded-xl bg-blue-50 px-2 py-3 transition hover:bg-blue-100"
                        >
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm ring-1 ring-blue-100">
                                <Mail class="h-4 w-4" />
                            </span>
                            <span class="text-xs font-medium text-blue-700">E-mel</span>
                        </a>

                        <div
                            v-if="contact.address_line_1"
                            class="flex flex-col items-center gap-1.5 rounded-xl bg-amber-50 px-2 py-3"
                        >
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-amber-600 shadow-sm ring-1 ring-amber-100">
                                <MapPin class="h-4 w-4" />
                            </span>
                            <span class="text-xs font-medium text-amber-700">Lokasi</span>
                        </div>
                    </div>

                    <!-- Address text below icons if present -->
                    <p v-if="contact.address_line_1" class="mt-3 text-center text-xs leading-relaxed text-slate-400">
                        {{ contact.address_line_1 }}<span v-if="contact.city">, {{ contact.city }}</span><span v-if="contact.state">, {{ contact.state }}</span><span v-if="contact.postcode">, {{ contact.postcode }}</span>
                    </p>
                </div>
            </section>

        </div>
        <PwaInstallBanner />
        <QrScannerModal
            :open="scannerOpen"
            title="Imbas QR Acara"
            @close="scannerOpen = false"
            @scanned="handleQrScan"
        />
    </MemberLayout>
</template>