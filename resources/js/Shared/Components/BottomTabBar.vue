<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { CreditCard, HandCoins, Home, Scan, UserRound } from 'lucide-vue-next';
import QrScannerModal from '@/Shared/Components/QrScannerModal.vue';

const page = usePage();
const scannerOpen = ref(false);

const emit = defineEmits(['open-menu']);

const tabs = [
    { label: 'Utama', href: '/member/dashboard', icon: Home },
    { label: 'Urusan', icon: HandCoins, isMenu: true },
    { label: 'Imbas', icon: Scan, isScan: true },
    { label: 'Kad', href: '/member/card', icon: CreditCard },
    { label: 'Akaun', href: '/member/profile', icon: UserRound },
];

const currentUrl = computed(() => page.url.split('?')[0]);

const isActive = (tab) => {
    if (tab.isScan) return false;
    if (tab.href === '/member/dashboard') {
        return currentUrl.value === '/member/dashboard';
    }
    return currentUrl.value.startsWith(tab.href);
};
</script>

<template>
    <QrScannerModal
        :open="scannerOpen"
        title="Imbas Kod QR"
        @close="scannerOpen = false"
    />

    <nav
        class="fixed bottom-0 left-0 right-0 z-40 flex justify-center lg:hidden"
    >
        <div
            class="relative mb-2 flex w-full items-end justify-around rounded-t-2xl bg-gradient-to-r from-white via-teal-50/60 to-blue-50/70 px-3 pb-1.5 pt-3 shadow-[0_-4px_24px_rgba(13,148,136,0.10)] backdrop-blur-xl ring-1 ring-teal-100"
            :style="{ paddingBottom: `env(safe-area-inset-bottom, 0px)` }"
        >
            <template v-for="tab in tabs" :key="tab.label">
                <!-- Center Scan QR FAB -->
                <button
                    v-if="tab.isScan"
                    type="button"
                    class="relative flex flex-1 flex-col items-center"
                    @click="scannerOpen = true"
                >
                    <div
                        class="-mt-5 flex h-[60px] w-[60px] items-center justify-center rounded-full bg-gradient-to-br from-teal-500 via-teal-500 to-emerald-500 text-white shadow-lg shadow-teal-500/35 ring-4 ring-white transition-all duration-200 ease-out hover:shadow-xl hover:shadow-teal-500/45 active:scale-90"
                    >
                        <component :is="tab.icon" class="h-7 w-7" />
                    </div>
                </button>

                <button
                    v-else-if="tab.isMenu"
                    type="button"
                    class="relative flex min-h-12 flex-1 flex-col items-center justify-center gap-1 pb-1 text-orange-500 transition-all duration-200 active:scale-95"
                    @click="emit('open-menu')"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-50">
                        <component :is="tab.icon" class="h-[18px] w-[18px]" />
                    </div>
                    <span class="text-[10px] font-medium leading-none">{{ tab.label }}</span>
                </button>

                <!-- Regular nav items -->
                <Link
                    v-else
                    :href="tab.href"
                    class="relative flex min-h-12 flex-1 flex-col items-center justify-center gap-1 pb-1 transition-all duration-200 active:scale-95"
                >
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-xl transition-all duration-200"
                        :class="isActive(tab)
                            ? 'bg-gradient-to-b from-teal-50 to-teal-100/70 text-teal-600 shadow-sm'
                            : 'text-slate-400'"
                    >
                        <component :is="tab.icon" class="h-[18px] w-[18px]" />
                    </div>
                    <span
                        class="text-[10px] font-medium leading-none transition-all duration-200"
                        :class="isActive(tab)
                            ? 'font-semibold text-teal-700'
                            : 'text-slate-400'"
                    >
                        {{ tab.label }}
                    </span>
                </Link>
            </template>
        </div>
    </nav>
</template>
