<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, ChevronDown, ChevronRight, LogIn, Menu, ShieldCheck, UserRound, X } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import AppLogo from '@/Shared/Components/AppLogo.vue';
import { Button } from '@/Shared/Components/ui/button';

const page = usePage();

const props = defineProps({
    menus: {
        type: Array,
        default: () => [],
    },
    variant: {
        type: String,
        default: 'solid',
        validator: (v) => ['solid', 'hero'].includes(v),
    },
    showSolidAfterScroll: {
        type: Number,
        default: 50,
    },
});

const cooperative = computed(() => page.props.appSettings?.cooperative ?? {});
const faviconUrl = computed(() => cooperative.value.favicon_url);

const mobileMenuOpen = ref(false);
const activeMobileMenu = ref(null);
const activeDropdown = ref(null);
const navRef = ref(null);
const scrolled = ref(false);

const navItems = computed(() => {
    if (props.menus?.length) {
        return props.menus.map(m => ({
            label: m.label,
            url: m.url || '#',
            children: (m.children || []).map(c => ({
                label: c.label,
                url: c.url || '#',
            })),
        }));
    }
    return [
        { label: 'Utama', url: '/' },
        { label: 'Pengumuman', url: '/pengumuman' },
        { label: 'Borang Online', url: '/forms' },
        { label: 'Ansuran Mudah', url: '/ansuran' },
    ];
});

const isHero = computed(() => props.variant === 'hero');

function toggleDropdown(label) {
    activeDropdown.value = activeDropdown.value === label ? null : label;
}
function toggleMobileMenu(label) {
    activeMobileMenu.value = activeMobileMenu.value === label ? null : label;
}
function closeAllDropdowns() { activeDropdown.value = null; }
function closeMobileMenu() {
    mobileMenuOpen.value = false;
    activeMobileMenu.value = null;
}

function handleScroll() {
    scrolled.value = window.scrollY > props.showSolidAfterScroll;
}
function handleKeydown(event) {
    if (event.key === 'Escape') closeMobileMenu();
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    if (isHero.value) {
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
    }
    document.addEventListener('keydown', handleKeydown);
});
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
    if (isHero.value) {
        window.removeEventListener('scroll', handleScroll);
    }
});

watch(mobileMenuOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : '';
});

function handleClickOutside(event) {
    if (navRef.value && !navRef.value.contains(event.target)) {
        closeAllDropdowns();
    }
}

const LOGIN_LABEL = 'Log Masuk';
const loginLinks = [
    { label: 'Admin', href: '/admin/login', icon: ShieldCheck },
    { label: 'Portal Ahli', href: '/member/login', icon: UserRound },
];

const isSolid = computed(() => !isHero.value || scrolled.value);
const linkClass = computed(() =>
    isSolid.value
        ? 'text-slate-600 hover:text-teal-700 hover:bg-teal-50'
        : 'text-white/90 hover:text-white hover:bg-white/10'
);
const dropdownClass = computed(() =>
    isSolid.value
        ? 'border-slate-200 bg-white shadow-xl shadow-slate-900/10'
        : 'border-slate-200 bg-white shadow-xl'
);
const dropdownItemClass = computed(() =>
    isSolid.value
        ? 'text-slate-700 hover:bg-teal-50 hover:text-teal-800'
        : 'text-slate-700 hover:bg-teal-50 hover:text-teal-800'
);
const headerBgClass = computed(() => {
    if (!isSolid.value) return 'bg-transparent';
    return 'bg-white/90 backdrop-blur-xl shadow-sm shadow-slate-200/40 border-b border-teal-900/10';
});
const mobileToggleClass = computed(() =>
    isSolid.value
        ? 'border-slate-200 bg-white text-slate-700 shadow-sm'
        : 'border-white/25 bg-slate-950/10 text-white backdrop-blur-sm'
);
</script>

<template>
    <Head>
        <link rel="icon" :href="faviconUrl || '/favicon.ico'" />
    </Head>

    <header
        class="top-0 z-50 transition-all duration-300"
        :class="[isHero ? 'fixed inset-x-0' : 'sticky', headerBgClass]"
    >
        <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:h-20 lg:px-8">
            <!-- Logo -->
            <div class="flex min-w-0 items-center gap-4">
                <AppLogo
                    :name="cooperative.name"
                    :logo-url="cooperative.logo_url"
                    href="/"
                    :size="isSolid ? 'md' : 'md'"
                />
            </div>

            <!-- Desktop Nav -->
            <nav ref="navRef" class="hidden min-w-0 flex-nowrap items-center gap-1 lg:flex">
                <div v-for="item in navItems" :key="item.label" class="relative">
                    <button
                        v-if="item.children?.length"
                        type="button"
                        class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-2 text-sm font-medium transition-colors xl:px-3.5"
                        :class="[linkClass, activeDropdown === item.label ? (isSolid ? 'bg-teal-50 text-teal-800' : 'bg-white/15 text-white') : '']"
                        @click.stop="toggleDropdown(item.label)"
                    >
                        {{ item.label }}
                        <ChevronDown class="ml-1 h-4 w-4 transition-transform duration-200" :class="activeDropdown === item.label ? 'rotate-180' : ''" />
                    </button>
                    <Link
                        v-else
                        :href="item.url || '#'"
                        class="inline-flex whitespace-nowrap rounded-full px-2.5 py-2 text-sm font-medium transition-colors xl:px-3.5"
                        :class="linkClass"
                    >
                        {{ item.label }}
                    </Link>

                    <Transition
                        enter-active-class="transition ease-out duration-150"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-1"
                    >
                        <div
                            v-if="item.children?.length && activeDropdown === item.label"
                            class="absolute left-0 top-full z-50 mt-1.5 w-64 rounded-2xl border p-2"
                            :class="dropdownClass"
                        >
                            <Link
                                v-for="child in item.children"
                                :key="child.url"
                                :href="child.url || '#'"
                                class="block whitespace-nowrap rounded-xl px-3 py-2.5 text-sm font-medium transition"
                                :class="dropdownItemClass"
                                @click="closeAllDropdowns"
                            >
                                {{ child.label }}
                            </Link>
                        </div>
                    </Transition>
                </div>
            </nav>

            <!-- Desktop CTA -->
            <div class="hidden items-center gap-3 lg:flex">
                <Button
                    as="a"
                    href="/membership/apply"
                    class="whitespace-nowrap px-3.5 xl:inline-flex"
                >
                    Mohon Jadi Ahli
                </Button>

                <div class="relative">
                    <Button
                        type="button"
                        variant="outline"
                        class="whitespace-nowrap px-3.5"
                        :class="activeDropdown === LOGIN_LABEL ? 'border-teal-300 bg-teal-50' : ''"
                        @click.stop="toggleDropdown(LOGIN_LABEL)"
                    >
                        <LogIn class="mr-2 h-4 w-4" />
                        {{ LOGIN_LABEL }}
                        <ChevronDown
                            class="ml-2 h-4 w-4 transition-transform duration-200"
                            :class="activeDropdown === LOGIN_LABEL ? 'rotate-180' : ''"
                        />
                    </Button>

                    <Transition
                        enter-active-class="transition ease-out duration-150"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-1"
                    >
                        <div
                            v-if="activeDropdown === LOGIN_LABEL"
                            class="absolute right-0 top-full z-50 mt-1.5 w-52 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10"
                        >
                            <Link
                                v-for="item in loginLinks"
                                :key="item.href"
                                :href="item.href"
                                class="flex items-center whitespace-nowrap rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-teal-50 hover:text-teal-800"
                                @click="closeAllDropdowns"
                            >
                                <component :is="item.icon" class="mr-2 h-4 w-4 text-teal-700" />
                                {{ item.label }}
                            </Link>
                        </div>
                    </Transition>
                </div>
            </div>

            <!-- Mobile Menu Toggle -->
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-full border transition lg:hidden"
                :class="mobileToggleClass"
                aria-label="Buka menu navigasi"
                aria-controls="mobile-navigation"
                :aria-expanded="mobileMenuOpen"
                @click="mobileMenuOpen = true"
            >
                <Menu class="h-6 w-6" />
            </button>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 bg-slate-950/55 backdrop-blur-sm lg:hidden" @click="closeMobileMenu">
        <aside id="mobile-navigation" class="ml-auto flex h-full w-[min(90vw,24rem)] flex-col overflow-hidden bg-slate-50 shadow-2xl" aria-label="Navigasi mobile" @click.stop>
            <div class="relative overflow-hidden bg-gradient-to-br from-[#082c3b] to-[#0F766E] px-5 pb-5 pt-4 text-white">
                <div class="absolute -right-10 -top-12 h-36 w-36 rounded-full bg-white/10" />
                <div class="relative flex items-center justify-between">
                    <AppLogo
                        :name="cooperative.name"
                        :logo-url="cooperative.logo_url"
                        href="/"
                        size="sm"
                        :dark="false"
                    />
                    <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20" aria-label="Tutup menu" @click="closeMobileMenu">
                        <X class="h-5 w-5" />
                    </button>
                </div>
                <div class="relative mt-5">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-teal-200">Menu Utama</p>
                    <p class="mt-1 text-sm text-white/70">Akses maklumat dan perkhidmatan koperasi.</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1.5 overflow-y-auto px-4 py-5">
                <div v-for="item in navItems" :key="item.label">
                    <button
                        v-if="item.children?.length"
                        type="button"
                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-[15px] font-semibold text-slate-700 transition hover:bg-white hover:text-teal-800 hover:shadow-sm"
                        :aria-expanded="activeMobileMenu === item.label"
                        @click="toggleMobileMenu(item.label)"
                    >
                        {{ item.label }}
                        <ChevronDown class="h-4 w-4 text-slate-400 transition-transform" :class="activeMobileMenu === item.label ? 'rotate-180 text-teal-600' : ''" />
                    </button>
                    <Link
                        v-else
                        :href="item.url || '#'"
                        class="flex items-center justify-between rounded-xl px-4 py-3 text-[15px] font-semibold text-slate-700 transition hover:bg-white hover:text-teal-800 hover:shadow-sm"
                        @click="closeMobileMenu"
                    >
                        {{ item.label }}
                        <ChevronRight class="h-4 w-4 text-slate-300" />
                    </Link>
                    <div
                        v-if="item.children?.length && activeMobileMenu === item.label"
                        class="mx-2 mb-2 mt-1 space-y-1 border-l-2 border-teal-200 pl-3"
                    >
                        <Link
                            v-for="child in item.children"
                            :key="child.url || child.label"
                            :href="child.url || '#'"
                            class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-teal-50 hover:text-teal-800"
                            @click="closeMobileMenu"
                        >
                            {{ child.label }}
                        </Link>
                    </div>
                </div>
            </nav>

            <div class="border-t border-slate-200 bg-white p-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <Link href="/membership/apply" class="flex w-full items-center justify-center gap-2 rounded-xl bg-teal-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800" @click="closeMobileMenu">
                    Mohon Jadi Ahli <ArrowRight class="h-4 w-4" />
                </Link>
                <Link href="/admin/login" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50" @click="closeMobileMenu">
                    <LogIn class="h-4 w-4" /> Log Masuk
                </Link>
            </div>
        </aside>
    </div>
</template>
