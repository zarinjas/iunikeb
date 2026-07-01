<script setup>
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight, Award, BadgePercent, Banknote, Building, Building2, CalendarDays,
    Check, FileText, Gift, HandCoins,
    HeartHandshake, Landmark, Play,
    ShieldCheck, ShoppingBag, Smartphone, Star, Store, TrendingUp,
    UserRound, Users, Wallet, Zap
} from 'lucide-vue-next';
import { computed } from 'vue';
import FlashToast from '@/Shared/Components/FlashToast.vue';
import PublicFooter from '@/Shared/Components/PublicFooter.vue';
import PublicNavbar from '@/Shared/Components/PublicNavbar.vue';

const props = defineProps({
    sections: { type: Object, required: true },
    menus: { type: Object, required: true },
});

const headerMenus = computed(() => props.menus?.header ?? []);
const footerMenusObj = computed(() => props.menus?.footer ?? {});

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

// Icon resolver — maps string names to Lucide components
const iconMap = {
    Users, Store, TrendingUp, CalendarDays, HandCoins, Wallet, ShieldCheck,
    Building2, ShoppingBag, Landmark, Smartphone, Zap, Star, Award,
    BadgePercent, Banknote, Building, Gift, HeartHandshake, Check,
    ArrowRight, FileText, Play,
};

function resolveIcon(name) {
    return iconMap[name] || Store;
}
</script>

<template>
    <div class="min-h-screen bg-white text-slate-800">
        <PublicNavbar :menus="headerMenus" variant="hero" />

        <!-- HERO SECTION -->
        <section class="relative h-[560px] overflow-hidden sm:h-[650px] lg:h-[760px]">
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

            <div class="relative mx-auto flex h-full max-w-7xl items-center px-5 pt-10 sm:px-6 sm:pt-0 lg:px-8">
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
                    <div class="mt-7 flex flex-col gap-3 min-[400px]:flex-row min-[400px]:flex-wrap sm:mt-8 sm:gap-4">
                        <a
                            v-if="hero.items?.[0]?.button_url"
                            :href="hero.items[0].button_url"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-[#0F8F83] px-6 py-3 font-semibold text-white transition hover:bg-[#0d7d73]"
                        >
                            {{ hero.items[0].button_text || 'Ketahui Lebih Lanjut' }}
                            <ArrowRight class="h-4 w-4" />
                        </a>
                        <a
                            v-if="hero.items?.[0]?.button_url"
                            href="#"
                            class="inline-flex items-center justify-center gap-2 rounded-full border border-white/30 px-6 py-3 font-semibold text-white transition hover:bg-white/10"
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
                <div class="grid grid-cols-2 gap-x-2 gap-y-6 divide-slate-100 lg:grid-cols-4 lg:divide-x">
                    <div v-for="item in stats.items" :key="item.id" class="flex flex-col items-center gap-2 px-1 text-center min-[400px]:flex-row min-[400px]:items-start min-[400px]:gap-3 min-[400px]:px-2 min-[400px]:text-left sm:px-4">
                        <component :is="resolveIcon(item.icon)" class="h-7 w-7 shrink-0 text-[#0F8F83] sm:h-8 sm:w-8" stroke-width="1.6" />
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
                        <div class="grid gap-5 px-7 pb-9 min-[440px]:grid-cols-2 min-[440px]:gap-x-5 min-[440px]:gap-y-6 lg:py-9 lg:pl-6 lg:pr-9">
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
                    <div class="relative z-10 max-w-[78%] sm:max-w-[58%]">
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
                    <div class="flex flex-col gap-4 min-[400px]:flex-row">
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
        <PublicFooter :footer-menus="footerMenusObj" />
    </div>

    <FlashToast />
</template>
