<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Building2, Calculator, CalendarCheck, CalendarDays, ChevronDown, CreditCard, FileCheck, FileText, Files, HandCoins, Home, ImagePlay, LogOut, Megaphone, Menu, MessagesSquare, ShoppingCart, Wallet, UserRound, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import BottomTabBar from '@/Shared/Components/BottomTabBar.vue';
import KoperasiAIChat from '@/Member/Components/KoperasiAIChat.vue';
import MemberPopup from '@/Shared/Components/MemberPopup.vue';
import ProfileAvatar from '@/Shared/Components/ProfileAvatar.vue';
import NotificationBell from '@/Shared/Components/NotificationBell.vue';
import { Button } from '@/Shared/Components/ui/button';

const page = usePage();
const sidebarOpen = ref(false);
const user = computed(() => page.props.auth?.user);
const navItems = computed(() => page.props.navigation?.member ?? []);
const currentUrl = computed(() => page.url);
const cooperative = computed(() => page.props.appSettings?.cooperative ?? {});
const cooperativeName = computed(() => cooperative.value.short_name || cooperative.value.name || '');
const logoPath = computed(() => cooperative.value.logo_url);
const faviconUrl = computed(() => cooperative.value.favicon_url);

const icons = {
    Calculator,
    CalendarCheck,
    CalendarDays,
    CreditCard,
    FileCheck,
    FileText,
    Files,
    HandCoins,
    Home,
    ImagePlay,
    Megaphone,
    MessagesSquare,
    ShoppingCart,
    Wallet,
    UserRound,
};

const itemTone = (label) => ({
    'Papan Pemuka': 'bg-blue-50 text-blue-600',
    'Kad Digital': 'bg-violet-50 text-violet-600',
    'Profil Saya': 'bg-cyan-50 text-cyan-600',
    Pembiayaan: 'bg-emerald-50 text-emerald-600',
    'Kalkulator Pembiayaan': 'bg-sky-50 text-sky-600',
    'Ansuran Mudah': 'bg-orange-50 text-orange-600',
    Program: 'bg-pink-50 text-pink-600',
    'Kehadiran Saya': 'bg-amber-50 text-amber-600',
    'Borang Online': 'bg-indigo-50 text-indigo-600',
    Aduan: 'bg-rose-50 text-rose-600',
    'Rujukan Saya': 'bg-lime-50 text-lime-700',
    'Caruman Saya': 'bg-teal-50 text-teal-600',
}[label] ?? 'bg-slate-100 text-slate-600');

const expandedMenus = ref(new Set());

const currentPath = computed(() => currentUrl.value.split('?')[0]);
const isActive = (href) => href && currentPath.value === new URL(href, window.location.origin).pathname;

const isChildActive = (children) => children?.some((c) => isActive(c.href));

const toggleMenu = (label) => {
    if (expandedMenus.value.has(label)) {
        expandedMenus.value.delete(label);
    } else {
        expandedMenus.value.add(label);
    }
};

watch(currentPath, () => {
    const activeParent = navItems.value.find((item) => item.children?.length && isChildActive(item.children));
    if (activeParent) expandedMenus.value = new Set([...expandedMenus.value, activeParent.label]);
}, { immediate: true });

const pageTitle = computed(() => {
    const titles = {
        'Member/Pages/Dashboard': 'Papan Pemuka',
        'Member/Pages/Card': 'Kad Digital',
        'Member/Pages/Profile': 'Profil Saya',
        'Member/Pages/Applications/Index': 'Hantaran Saya',
        'Member/Pages/Applications/Show': 'Hantaran Saya',
        'Member/Pages/Forms/Index': 'Borang Online',
        'Member/Pages/Financing/Index': 'Pembiayaan',
        'Member/Pages/Financing/ProductShow': 'Pembiayaan',
        'Member/Pages/Financing/Calculator': 'Kalkulator Pembiayaan',
        'Member/Pages/Financing/Applications/Index': 'Pembiayaan',
        'Member/Pages/Financing/Applications/Create': 'Pembiayaan',
        'Member/Pages/Financing/Applications/Show': 'Pembiayaan',
        'Member/Pages/Financing/Applications/Print': 'Pembiayaan',
        'Member/Pages/Financing/GuarantorRequests/Index': 'Penjamin',
        'Member/Pages/Financing/GuarantorRequests/Show': 'Penjamin',
        'Member/Pages/Announcements/Index': 'Pengumuman',
        'Member/Pages/Complaints/Index': 'Aduan',
        'Member/Pages/Complaints/Create': 'Aduan',
        'Member/Pages/Complaints/Show': 'Aduan',
        'Member/Pages/Documents/Index': 'Dokumen',
        'Member/Pages/Caruman/Index': 'Caruman Saya',
        'Member/Pages/Programs/Index': 'Program',
        'Member/Pages/Programs/Show': 'Program',
        'Member/Pages/Programs/CheckIn': 'Daftar Masuk',
        'Member/Pages/Attendance/Index': 'Kehadiran Saya',
        'Member/Pages/Ansuran/Catalog': 'Ansuran Mudah',
        'Member/Pages/Ansuran/ProductDetail': 'Ansuran Mudah',
        'Member/Pages/Ansuran/MyApplications': 'Permohonan Saya',
        'Member/Pages/Ansuran/ApplicationDetail': 'Permohonan Saya',
        'Member/Pages/Ansuran/Sign': 'Tandatangan Perjanjian',
        'Member/Pages/Ansuran/ApplyConfirmation': 'Permohonan Dihantar',
        'Member/Pages/Ansuran/GuarantorRequests': 'Permintaan Penjamin',
        'Member/Pages/Placeholder': 'Portal Ahli',
    };
    return titles[page.component] ?? 'Portal Ahli';
});

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head>
        <link rel="icon" :href="faviconUrl || '/favicon.ico'" />
    </Head>

    <MemberPopup v-if="page.props.popup" :popup="page.props.popup" />

    <div class="relative min-h-screen bg-gradient-to-b from-blue-100/40 via-white to-sky-100/25 text-slate-950">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 overflow-y-auto border-r border-slate-200 bg-white lg:block">
            <div class="flex h-16 items-center gap-3 border-b border-teal-100 bg-gradient-to-r from-teal-50 via-cyan-50/60 to-blue-50 px-6">
                <Link href="/member/dashboard" class="flex items-center gap-3 font-semibold">
                    <span v-if="logoPath" class="flex h-9 w-9 items-center justify-center rounded-lg">
                        <img :src="logoPath" :alt="cooperativeName" class="h-7 w-7 rounded object-contain" />
                    </span>
                    <span v-else class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-700 text-white">
                        <Building2 class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold">{{ cooperativeName }}</p>
                        <p class="text-xs text-slate-500">Portal Ahli</p>
                    </div>
                </Link>
            </div>

            <nav class="space-y-1 px-4 py-5">
                <template v-for="item in navItems" :key="item.label">
                    <!-- Parent item with children -->
                    <div v-if="item.children">
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                            :class="isChildActive(item.children) ? 'bg-gradient-to-r from-teal-100/80 to-cyan-50 text-teal-900 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'"
                            @click="toggleMenu(item.label)"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl" :class="itemTone(item.label)"><component :is="icons[item.icon] ?? Home" class="h-4 w-4" /></span>
                            <span class="flex-1 text-left">{{ item.label }}</span>
                            <ChevronDown
                                class="h-4 w-4 transition-transform"
                                :class="expandedMenus.has(item.label) ? 'rotate-0' : '-rotate-90'"
                            />
                        </button>
                        <div v-show="expandedMenus.has(item.label)" class="ml-2 mt-1 space-y-0.5 border-l-2 border-teal-200 pl-2">
                            <Link
                                v-for="child in item.children"
                                :key="child.href"
                                :href="child.href"
                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                                :class="isActive(child.href) ? 'bg-teal-50 text-teal-800' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'"
                            >
                                {{ child.label }}
                            </Link>
                        </div>
                    </div>
                    <!-- Flat link item -->
                    <Link
                        v-else
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="isActive(item.href) ? 'bg-gradient-to-r from-teal-100/80 to-cyan-50 text-teal-900 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl" :class="itemTone(item.label)"><component :is="icons[item.icon] ?? Home" class="h-4 w-4" /></span>
                        {{ item.label }}
                    </Link>
                </template>
            </nav>
        </aside>

        <div
            class="fixed inset-0 z-50 transition-all duration-300 ease-in-out lg:hidden"
            :class="sidebarOpen ? 'pointer-events-auto opacity-100' : 'pointer-events-none opacity-0'"
        >
            <div class="absolute inset-0 bg-slate-950/40" @click="sidebarOpen = false" />
            <aside
                class="absolute left-0 top-0 flex h-full w-full max-w-xs flex-col bg-white shadow-xl transition-transform duration-300 ease-in-out"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                @click.stop
            >
                <div class="flex h-16 items-center justify-between border-b border-teal-100 bg-gradient-to-r from-teal-50 via-cyan-50/60 to-blue-50 px-6">
                    <Link href="/member/dashboard" class="flex items-center gap-3 font-semibold" @click="sidebarOpen = false">
                        <span v-if="logoPath" class="flex h-9 w-9 items-center justify-center rounded-lg">
                            <img :src="logoPath" :alt="cooperativeName" class="h-7 w-7 rounded object-contain" />
                        </span>
                        <span v-else class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-700 text-white">
                            <Building2 class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold">{{ cooperativeName }}</p>
                            <p class="text-xs text-slate-500">Portal Ahli</p>
                        </div>
                    </Link>
                    <Button type="button" variant="ghost" size="icon" @click="sidebarOpen = false">
                        <X class="h-5 w-5" />
                    </Button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                    <template v-for="item in navItems" :key="item.label">
                        <div v-if="item.children">
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                                :class="isChildActive(item.children) ? 'bg-gradient-to-r from-teal-100/80 to-cyan-50 text-teal-900 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'"
                                @click="toggleMenu(item.label)"
                            >
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl" :class="itemTone(item.label)"><component :is="icons[item.icon] ?? Home" class="h-4 w-4" /></span>
                                <span class="flex-1 text-left">{{ item.label }}</span>
                                <ChevronDown
                                    class="h-4 w-4 transition-transform"
                                    :class="expandedMenus.has(item.label) ? 'rotate-0' : '-rotate-90'"
                                />
                            </button>
                            <div v-show="expandedMenus.has(item.label)" class="ml-2 mt-1 space-y-0.5 border-l-2 border-teal-200 pl-2">
                                <Link
                                    v-for="child in item.children"
                                    :key="child.href"
                                    :href="child.href"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                                    :class="isActive(child.href) ? 'bg-teal-50 text-teal-800' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'"
                                    @click="sidebarOpen = false"
                                >
                                    {{ child.label }}
                                </Link>
                            </div>
                        </div>
                        <Link
                            v-else
                            :href="item.href"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                            :class="isActive(item.href) ? 'bg-gradient-to-r from-teal-100/80 to-cyan-50 text-teal-900 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'"
                            @click="sidebarOpen = false"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl" :class="itemTone(item.label)"><component :is="icons[item.icon] ?? Home" class="h-4 w-4" /></span>
                            {{ item.label }}
                        </Link>
                    </template>
                </nav>

                <div class="border-t border-slate-200 px-4 py-4">
                    <div class="mb-4 flex items-center gap-3 text-sm">
                        <ProfileAvatar :photo-url="user?.profile_photo_url" :name="user?.name" size="sm" />
                        <div class="min-w-0">
                            <p class="truncate font-medium text-slate-950">{{ user?.name }}</p>
                            <p class="text-slate-500">Akaun ahli</p>
                        </div>
                    </div>
                    <Button type="button" variant="outline" class="w-full" @click="logout">
                        <LogOut class="mr-2 h-4 w-4" />
                        Log Keluar
                    </Button>
                </div>
            </aside>
        </div>

        <div class="lg:pl-72">
            <!-- Mobile header: hamburger + centered title + notification -->
            <header class="sticky top-0 z-30 flex min-h-14 items-center justify-center border-b border-slate-200 bg-white/95 backdrop-blur lg:hidden" :style="{ paddingTop: 'env(safe-area-inset-top, 0px)' }">
                <Button type="button" variant="ghost" size="icon" class="absolute left-2 z-10" @click="sidebarOpen = true">
                    <Menu class="h-5 w-5" />
                </Button>
                <p class="truncate px-14 text-sm font-semibold text-slate-950">{{ pageTitle }}</p>
                <div class="absolute right-2 z-10">
                    <NotificationBell />
                </div>
            </header>

            <!-- Desktop header -->
            <header class="sticky top-0 z-30 hidden border-b border-slate-200 bg-white/95 backdrop-blur lg:block">
                <div class="flex min-h-16 items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">Portal Ahli</p>
                            <p class="hidden truncate text-xs text-slate-500 sm:block">Akses maklumat dan urusan keahlian anda</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <NotificationBell />
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-medium">{{ user?.name }}</p>
                            <p class="text-xs text-slate-500">Akaun ahli</p>
                        </div>
                        <Button type="button" variant="outline" class="hidden sm:inline-flex" @click="logout">
                            <LogOut class="mr-2 h-4 w-4" />
                            Log Keluar
                        </Button>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 pb-24 sm:px-6 lg:px-8 lg:pb-6">
                <slot />
            </main>

            <BottomTabBar @open-menu="sidebarOpen = true" />
            <KoperasiAIChat />
        </div>
    </div>
</template>
