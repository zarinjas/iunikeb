<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight, Award, BadgePercent, Banknote, Building, Building2, CalendarDays,
    Check, ChevronDown, ChevronRight, FileText, Gift, HandCoins,
    HeartHandshake, Landmark, LogIn, Mail, MapPin, Menu, Phone, Play,
    ShieldCheck, ShoppingBag, Smartphone, Star, Store, TrendingUp,
    UserRound, Users, Wallet, X, Zap
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import AppLogo from '@/Shared/Components/AppLogo.vue';
import FlashToast from '@/Shared/Components/FlashToast.vue';
import { Button } from '@/Shared/Components/ui/button';

const page = usePage();
const props = defineProps({
    sections: { type: Object, required: true },
    menus: { type: Object, required: true },
});

const cooperative = computed(() => page.props.appSettings?.cooperative ?? {});
const faviconUrl = computed(() => cooperative.value.favicon_url);
const contact = computed(() => page.props.appSettings?.contact ?? {});
const mobileMenuOpen = ref(false);
const activeDropdown = ref(null);
const navRef = ref(null);
const scrolled = ref(false);

function toggleDropdown(label) { activeDropdown.value = activeDropdown.value === label ? null : label; }
function closeAllDropdowns() { activeDropdown.value = null; }

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', () => { scrolled.value = window.scrollY > 50; });
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

function handleClickOutside(event) {
    if (navRef.value && !navRef.value.contains(event.target)) closeAllDropdowns();
}

// Section data helpers
const hero = computed(() => props.sections?.hero ?? { items: [] });
const stats = computed(() => props.sections?.stats ?? { items: [] });
const services = computed(() => props.sections?.services ?? { items: [] });
const benefit = computed(() => props.sections?.benefit ?? { items: [] });
const benefitBgStyle = computed(() => {
    const bg = benefit.value.data?.background_image;
    if (!bg) return {};
    return {
        backgroundImage: `url(${bg.startsWith('http') ? bg : '/storage/' + bg})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
    };
});
function hexToRgba(hex, alpha) {
    const c = hex.replace('#', '');
    const r = parseInt(c.substring(0, 2), 16);
    const g = parseInt(c.substring(2, 4), 16);
    const b = parseInt(c.substring(4, 6), 16);
    return `rgba(${r},${g},${b},${alpha})`;
}
const benefitOverlayStyle = computed(() => {
    const data = benefit.value.data || {};
    if (!data.overlay_enabled || !data.overlay_color) return {};
    const opacity = data.overlay_opacity != null ? data.overlay_opacity / 100 : 1;
    if (data.overlay_type === 'gradient') {
        const angle = data.overlay_gradient_angle || 180;
        const c2 = data.overlay_gradient_color_to || 'transparent';
        return { backgroundImage: `linear-gradient(${angle}deg, ${hexToRgba(data.overlay_color, opacity)}, ${c2})` };
    }
    return { backgroundColor: hexToRgba(data.overlay_color, opacity) };
});
const benefitGradientEnabled = computed(() => benefit.value.data?.decorative_enabled);
const benefitGradientStyle = computed(() => {
    const data = benefit.value.data || {};
    if (!data.decorative_color) return {};
    const angle = data.decorative_angle || 135;
    return { backgroundImage: `linear-gradient(${angle}deg, ${data.decorative_color}, transparent)` };
});
const business = computed(() => props.sections?.business ?? { items: [] });
const promotion = computed(() => props.sections?.promotion ?? { items: [] });
const membership = computed(() => props.sections?.membership ?? { items: [] });
const footerSection = computed(() => props.sections?.footer ?? {});
const footerLinks = computed(() => {
    const groups = Object.values(props.menus?.footer ?? {});
    return groups.flat().slice(0, 6);
});

// Static navigation fallback
const navItems = computed(() => {
    if (props.menus?.header?.length) return props.menus.header;
    return [
        { label: 'Utama', url: '/' },
        { label: 'Profil', url: '/profil', children: [] },
        { label: 'Perkhidmatan', url: '/perkhidmatan', children: [] },
        { label: 'Perniagaan', url: '/perniagaan', children: [] },
        { label: 'Maklumat', url: '/maklumat', children: [] },
        { label: 'Hubungi', url: '/hubungi' },
    ];
});

// Icon resolver — maps string names to Lucide components
const iconMap = {
    Users, Store, TrendingUp, CalendarDays, HandCoins, Wallet, ShieldCheck,
    Building2, ShoppingBag, Landmark, Smartphone, Zap, Star, Award,
    BadgePercent, Banknote, Building, Gift, HeartHandshake, Check,
    ArrowRight, Mail, MapPin, Phone, FileText, Play,
};

function resolveIcon(name) {
    return iconMap[name] || Store;
}

const address = computed(() => [
    contact.value.address_line_1,
    contact.value.address_line_2,
    [contact.value.postcode, contact.value.city, contact.value.state].filter(Boolean).join(' '),
].filter(Boolean).join(', '));
</script>

<template>
    <Head>
        <link rel="icon" :href="faviconUrl || '/favicon.ico'" />
    </Head>

    <div class="min-h-screen bg-white text-slate-800">
        <!-- HEADER -->
        <header
            class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
            :class="scrolled ? 'bg-white shadow-sm border-b border-slate-200' : 'bg-transparent'"
        >
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:h-20 lg:px-8">
                <AppLogo :name="cooperative.name" :logo-url="cooperative.logo_url" href="/" size="xl" :show-text="false" />

                <nav ref="navRef" class="hidden items-center gap-1 lg:flex">
                    <div v-for="item in navItems" :key="item.label" class="relative">
                        <button
                            v-if="item.children?.length"
                            type="button"
                            class="rounded-full px-3 py-2 text-sm font-medium transition-colors"
                            :class="scrolled ? 'text-slate-600 hover:text-teal-700 hover:bg-teal-50' : 'text-white/90 hover:text-white hover:bg-white/10'"
                            @click.stop="toggleDropdown(item.label)"
                        >
                            {{ item.label }}
                            <ChevronDown class="ml-1 inline h-4 w-4" />
                        </button>
                        <Link
                            v-else
                            :href="item.url || '#'"
                            class="rounded-full px-3 py-2 text-sm font-medium transition-colors"
                            :class="scrolled ? 'text-slate-600 hover:text-teal-700 hover:bg-teal-50' : 'text-white/90 hover:text-white hover:bg-white/10'"
                        >
                            {{ item.label }}
                        </Link>
                        <div
                            v-if="item.children?.length && activeDropdown === item.label"
                            class="absolute left-0 top-full z-50 mt-1 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"
                        >
                            <Link
                                v-for="child in item.children"
                                :key="child.url"
                                :href="child.url || '#'"
                                class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-teal-50 hover:text-teal-800"
                                @click="closeAllDropdowns"
                            >
                                {{ child.label }}
                            </Link>
                        </div>
                    </div>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    <a
                        href="/membership/apply"
                        class="rounded-full px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0d7d73]"
                        :class="scrolled ? 'bg-[#0F8F83]' : 'bg-[#0F8F83]'"
                    >
                        Mohon Jadi Ahli
                    </a>
                    <Link
                        href="/admin/login"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition-colors"
                        :class="scrolled
                            ? 'border-slate-300 text-slate-700 hover:bg-slate-50'
                            : 'border-white/30 text-white hover:bg-white/10'"
                    >
                        <LogIn class="h-4 w-4" />
                        Log Masuk
                    </Link>
                </div>

                <button type="button" class="lg:hidden" :class="scrolled ? 'text-slate-700' : 'text-white'" @click="mobileMenuOpen = true">
                    <Menu class="h-6 w-6" />
                </button>
            </div>
        </header>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-sm lg:hidden" @click="mobileMenuOpen = false">
            <aside class="ml-auto flex h-full w-80 flex-col bg-white shadow-2xl" @click.stop>
                <div class="flex h-16 items-center justify-between border-b px-5">
                    <AppLogo :name="cooperative.name" :logo-url="cooperative.logo_url" href="/" size="sm" />
                    <button @click="mobileMenuOpen = false"><X class="h-5 w-5" /></button>
                </div>
                <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                    <Link v-for="item in navItems" :key="item.label" :href="item.url || '#'" class="block rounded-xl px-3 py-3 font-semibold text-slate-800 hover:bg-teal-50" @click="mobileMenuOpen = false">
                        {{ item.label }}
                    </Link>
                </nav>
            </aside>
        </div>

        <!-- HERO SECTION -->
        <section class="relative h-[600px] overflow-hidden sm:h-[650px] lg:h-[760px]">
            <div v-if="hero.items?.length" class="absolute inset-0">
                <div
                    v-for="(slide, i) in hero.items"
                    :key="slide.id"
                    class="absolute inset-0 transition-opacity duration-700"
                    :class="i === 0 ? 'opacity-100' : 'opacity-0'"
                >
                    <img
                        v-if="slide.image"
                        :src="slide.image"
                        :alt="slide.title"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>
            <div v-else class="absolute inset-0 bg-gradient-to-br from-teal-900 via-teal-800 to-slate-900" />
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/50 via-slate-950/40 to-slate-950/75" />

            <div class="relative mx-auto flex h-full max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <div class="max-w-xl lg:ml-0">
                    <p v-if="hero.items?.[0]?.subtitle" class="text-sm font-semibold uppercase tracking-widest text-teal-300">
                        {{ hero.items[0].subtitle }}
                    </p>
                    <h1 class="mt-4 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                        {{ hero.items?.[0]?.title || 'Selamat Datang ke Koperasi' }}
                    </h1>
                    <p v-if="hero.items?.[0]?.description" class="mt-4 max-w-lg text-base leading-relaxed text-slate-200 sm:text-lg">
                        {{ hero.items[0].description }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a
                            v-if="hero.items?.[0]?.button_url"
                            :href="hero.items[0].button_url"
                            class="inline-flex items-center gap-2 rounded-full bg-[#0F8F83] px-6 py-3 font-semibold text-white transition hover:bg-[#0d7d73]"
                        >
                            {{ hero.items[0].button_text || 'Ketahui Lebih Lanjut' }}
                            <ArrowRight class="h-4 w-4" />
                        </a>
                        <a
                            v-if="hero.items?.[0]?.button_url"
                            href="#"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 px-6 py-3 font-semibold text-white transition hover:bg-white/10"
                        >
                            <Play class="h-4 w-4 fill-white" />
                            Tonton Video
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATS SECTION -->
        <section class="relative z-10 -mt-14 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl rounded-2xl border border-slate-100 bg-white px-5 py-5 shadow-[0_12px_35px_rgba(15,23,42,0.10)] sm:px-7">
                <div class="grid grid-cols-2 gap-y-6 divide-slate-100 lg:grid-cols-4 lg:divide-x">
                    <div v-for="item in stats.items" :key="item.id" class="flex items-center gap-3 px-2 sm:px-4">
                        <component :is="resolveIcon(item.icon)" class="h-8 w-8 shrink-0 text-[#0F8F83]" stroke-width="1.6" />
                        <div>
                            <div class="text-lg font-bold leading-tight text-slate-900 sm:text-xl">{{ item.value || '...' }}</div>
                            <div class="mt-1 text-xs leading-tight text-slate-500">{{ item.title || '...' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SERVICES SECTION -->
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-[0.8fr_2.2fr] lg:gap-7">
                    <div class="flex flex-col justify-center lg:pr-5">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#0F8F83]">Perkhidmatan Kami</p>
                        <h2 class="mt-3 text-3xl font-bold leading-tight text-slate-900">
                            {{ services.title || 'Perkhidmatan Untuk Ahli' }}
                        </h2>
                        <div class="mt-4 h-1 w-10 rounded-full bg-[#0F8F83]" />
                        <p class="mt-5 text-sm leading-6 text-slate-500">
                            {{ services.subtitle || 'Kami menyediakan pelbagai perkhidmatan berkualiti bagi memenuhi keperluan ahli.' }}
                        </p>
                        <a href="#perkhidmatan" class="mt-6 inline-flex w-fit items-center gap-2 rounded-lg border border-teal-200 px-4 py-2.5 text-sm font-semibold text-[#0F8F83] transition hover:bg-teal-50">
                            Lihat Semua Perkhidmatan <ArrowRight class="h-4 w-4" />
                        </a>
                    </div>

                    <div id="perkhidmatan" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <article v-for="item in services.items?.slice(0, 3)" :key="item.id" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_6px_20px_rgba(15,23,42,0.05)] transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative h-40 overflow-hidden bg-slate-100">
                                <img v-if="item.image" :src="item.image" :alt="item.title" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                <div v-else class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-teal-50 text-teal-700/25">
                                    <component :is="resolveIcon(item.icon)" class="h-16 w-16" stroke-width="1.3" />
                                </div>
                                <div v-if="!item.image" class="absolute -bottom-5 left-4 flex h-11 w-11 items-center justify-center rounded-full border-4 border-white bg-[#0F8F83] text-white shadow-sm">
                                    <component :is="resolveIcon(item.icon)" class="h-5 w-5" />
                                </div>
                            </div>
                            <div class="flex min-h-44 flex-col px-5 pb-5 pt-8">
                                <h3 class="font-bold text-slate-900">{{ item.title }}</h3>
                                <p v-if="item.description" class="mt-2 flex-1 text-xs leading-5 text-slate-500">{{ item.description }}</p>
                                <a :href="item.button_url || '#'" class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-[#0F8F83]">
                                    {{ item.button_text || 'Maklumat Lanjut' }} <ArrowRight class="h-3.5 w-3.5 transition group-hover:translate-x-1" />
                                </a>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- BENEFIT SECTION -->
        <section class="pb-16 sm:pb-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden rounded-2xl text-white shadow-sm"
                    :class="{ 'bg-[#082c3b]': !benefitBgStyle.backgroundImage }"
                    :style="benefitBgStyle"
                >
                    <div v-if="benefitBgStyle.backgroundImage" class="absolute inset-0" :style="benefitOverlayStyle" />
                    <div v-if="benefitBgStyle.backgroundImage && benefitGradientEnabled" class="absolute inset-0" :style="benefitGradientStyle" />
                    <div v-else-if="!benefitBgStyle.backgroundImage" class="absolute inset-0 bg-[radial-gradient(circle_at_85%_20%,rgba(20,184,166,0.16),transparent_38%)]" />
                    <div class="relative z-10 grid lg:grid-cols-[1fr_1.25fr]">
                        <div class="flex flex-col justify-center px-7 py-8 lg:px-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-teal-300">Kepentingan Keanggotaan</p>
                            <h2 class="mt-3 text-2xl font-bold">{{ benefit.title || 'Dalam Membantu Koperasi' }}</h2>
                            <p class="mt-4 text-sm leading-6 text-slate-300">{{ benefit.subtitle || 'Sokongan dan penyertaan ahli menjadi tunjang kekuatan koperasi.' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-x-5 gap-y-6 px-7 pb-9 lg:py-9 lg:pl-6 lg:pr-9">
                            <div v-for="item in benefit.items?.slice(0, 4)" :key="item.id" class="flex gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-teal-400/30 text-teal-300">
                                    <component :is="resolveIcon(item.icon)" class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold">{{ item.title }}</h3>
                                    <p v-if="item.description" class="mt-1 text-xs leading-5 text-slate-400">{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- BUSINESS SECTION -->
        <section v-if="business.items?.length" class="pb-16 sm:pb-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="mb-7 flex items-end justify-between gap-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#0F8F83]">Perniagaan Koperasi</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">{{ business.title || 'Perniagaan Milik Koperasi' }}</h2>
                    </div>
                    <a href="#" class="hidden items-center gap-2 rounded-lg border border-teal-200 px-4 py-2 text-sm font-semibold text-[#0F8F83] transition hover:bg-teal-50 sm:inline-flex">
                        Lihat Semua <ArrowRight class="h-4 w-4" />
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <article v-for="item in business.items?.slice(0, 6)" :key="item.id" class="group">
                        <div class="h-28 overflow-hidden rounded-xl bg-slate-100 sm:h-32">
                            <img v-if="item.image" :src="item.image" :alt="item.title" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            <div v-else class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                                <Store class="h-8 w-8" stroke-width="1.5" />
                            </div>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold leading-5 text-slate-900">{{ item.title }}</h3>
                    </article>
                </div>
            </div>
        </section>

        <!-- PROMOTION SECTION -->
        <section class="pb-16 sm:pb-20">
            <div class="mx-auto grid max-w-6xl gap-5 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                <article v-for="(item, index) in promotion.items?.slice(0, 2)" :key="item.id" class="relative min-h-64 overflow-hidden rounded-2xl p-7 text-white sm:p-9" :class="index === 0 ? 'bg-[#082c3b]' : 'bg-[#078579]'">
                    <div class="relative z-10 max-w-[58%]">
                        <p v-if="item.subtitle" class="text-xs font-bold uppercase tracking-[0.16em] text-teal-200">{{ item.subtitle }}</p>
                        <h2 class="mt-2 text-2xl font-bold leading-tight">{{ item.title }}</h2>
                        <p v-if="item.description" class="mt-3 text-sm leading-6 text-white/75">{{ item.description }}</p>
                        <a v-if="item.button_url" :href="item.button_url" class="mt-5 inline-flex items-center gap-2 rounded-lg border border-white/35 px-4 py-2.5 text-sm font-semibold transition hover:bg-white/10">
                            {{ item.button_text || 'Ketahui Lanjut' }} <ArrowRight class="h-4 w-4" />
                        </a>
                    </div>
                    <img v-if="item.image" :src="item.image" :alt="item.title" class="absolute bottom-0 right-0 h-[92%] w-[46%] object-contain object-bottom" />
                    <Smartphone v-else-if="index === 0" class="absolute -bottom-9 right-5 h-56 w-32 rotate-6 text-white/15" stroke-width="1" />
                    <UserRound v-else class="absolute -bottom-7 right-6 h-52 w-52 text-white/12" stroke-width="1" />
                </article>

                <template v-if="!promotion.items?.length">
                    <article class="relative min-h-64 overflow-hidden rounded-2xl bg-[#082c3b] p-7 text-white sm:p-9">
                        <div class="relative z-10 max-w-[62%]">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-teal-200">Muat Turun Sekarang</p>
                            <h2 class="mt-2 text-2xl font-bold">Aplikasi Koperasi</h2>
                            <p class="mt-3 text-sm leading-6 text-white/70">Urus keahlian dan nikmati akses mudah ke perkhidmatan koperasi.</p>
                        </div>
                        <Smartphone class="absolute -bottom-9 right-5 h-56 w-32 rotate-6 text-white/15" stroke-width="1" />
                    </article>
                    <article class="relative min-h-64 overflow-hidden rounded-2xl bg-[#078579] p-7 text-white sm:p-9">
                        <div class="relative z-10 max-w-[62%]">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-teal-100">Jom Jadi Ahli</p>
                            <h2 class="mt-2 text-2xl font-bold">Keahlian Untuk Masa Depan Anda</h2>
                            <a href="/membership/apply" class="mt-5 inline-flex items-center gap-2 rounded-lg border border-white/35 px-4 py-2.5 text-sm font-semibold">Daftar Sekarang <ArrowRight class="h-4 w-4" /></a>
                        </div>
                        <UserRound class="absolute -bottom-7 right-6 h-52 w-52 text-white/15" stroke-width="1" />
                    </article>
                </template>
            </div>
        </section>

        <!-- MEMBERSHIP CTA -->
        <section class="pb-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-7 rounded-2xl bg-slate-50 px-6 py-8 sm:px-8 lg:grid-cols-[1.25fr_2fr] lg:items-center">
                    <div class="flex gap-4">
                        <Users class="h-11 w-11 shrink-0 text-[#0F8F83]" stroke-width="1.5" />
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">{{ membership.title || 'Berminat Menjadi Ahli Koperasi?' }}</h2>
                            <p class="mt-2 text-xs leading-5 text-slate-500">{{ membership.subtitle || 'Sertai kami hari ini dan nikmati pelbagai manfaat sebagai ahli.' }}</p>
                            <a href="/membership/apply" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-teal-200 px-4 py-2 text-xs font-semibold text-[#0F8F83]">Daftar Sekarang <ArrowRight class="h-3.5 w-3.5" /></a>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                        <div v-for="item in membership.items?.slice(0, 4)" :key="item.id" class="text-center">
                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-[#0F8F83] shadow-sm">
                                <component :is="resolveIcon(item.icon || 'TrendingUp')" class="h-5 w-5" />
                            </div>
                            <div class="mt-2 text-xs text-slate-500">{{ item.title }}</div>
                            <div class="mt-1 text-base font-bold text-slate-900">{{ item.value || '...' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="bg-[#071d2b] text-slate-300">
            <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-[1.45fr_0.75fr_1.15fr_0.85fr] lg:px-8">
                <div>
                    <AppLogo :name="cooperative.name" :logo-url="cooperative.logo_url" href="/" size="sm" :dark="false" />
                    <p class="mt-5 max-w-sm text-xs leading-6 text-slate-400">
                        {{ footerSection.subtitle || cooperative.description || `${cooperative.name} komited menyediakan perkhidmatan dan peluang yang memberi manfaat kepada ahli.` }}
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">Pautan Pantas</h3>
                    <nav class="mt-4 grid gap-2.5">
                        <Link v-for="item in footerLinks" :key="`${item.label}-${item.url}`" :href="item.url || '#'" class="text-xs text-slate-400 transition hover:text-teal-300">{{ item.label }}</Link>
                        <template v-if="!footerLinks.length">
                            <Link v-for="item in navItems.slice(0, 5)" :key="item.label" :href="item.url || '#'" class="text-xs text-slate-400 transition hover:text-teal-300">{{ item.label }}</Link>
                        </template>
                    </nav>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">Hubungi Kami</h3>
                    <div class="mt-4 grid gap-3 text-xs leading-5 text-slate-400">
                        <div class="flex items-start gap-2.5"><MapPin class="mt-0.5 h-4 w-4 shrink-0 text-teal-400" /><span>{{ address || 'Alamat akan dikemas kini' }}</span></div>
                        <div class="flex items-center gap-2.5"><Phone class="h-4 w-4 shrink-0 text-teal-400" /><span>{{ contact.phone || 'Telefon akan dikemas kini' }}</span></div>
                        <div class="flex items-center gap-2.5"><Mail class="h-4 w-4 shrink-0 text-teal-400" /><span>{{ contact.email || 'E-mel akan dikemas kini' }}</span></div>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">Waktu Operasi</h3>
                    <div class="mt-4 space-y-2 text-xs leading-5 text-slate-400">
                        <p>Isnin - Jumaat</p>
                        <p class="text-slate-300">8:30 pagi - 5:30 petang</p>
                        <p class="pt-2">Sabtu - Ahad</p>
                        <p class="text-slate-500">Tutup</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10">
                <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-sm text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <span>&copy; {{ new Date().getFullYear() }} {{ cooperative.name }}. Hak Cipta Terpelihara.</span>
                    <div class="flex items-center gap-6">
                        <Link href="#" class="hover:text-slate-300">Dasar Privasi</Link>
                        <Link href="#" class="hover:text-slate-300">Terma Penggunaan</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <FlashToast />
</template>
