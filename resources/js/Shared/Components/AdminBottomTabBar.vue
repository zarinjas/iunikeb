<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, ClipboardCheck, Home, Menu, Users } from 'lucide-vue-next';

const props = defineProps({
    navigation: { type: Array, default: () => [] },
});

const emit = defineEmits(['open-menu']);
const page = usePage();
const currentPath = computed(() => page.url.split('?')[0]);

const findItem = (label) => props.navigation.find((item) => item.label === label);
const tabs = computed(() => [
    { label: 'Utama', href: '/admin/dashboard', icon: Home, exact: true, tone: 'bg-blue-50 text-blue-600' },
    findItem('Semakan') && { label: 'Tugasan', href: findItem('Semakan').href, icon: ClipboardCheck, badge: findItem('Semakan').badge, tone: 'bg-amber-50 text-amber-600' },
    findItem('Ahli') && { label: 'Ahli', href: findItem('Ahli').href, icon: Users, tone: 'bg-teal-50 text-teal-600' },
    { label: 'Notifikasi', href: '/admin/notifications', icon: Bell, tone: 'bg-violet-50 text-violet-600' },
].filter(Boolean));

const isActive = (tab) => tab.exact
    ? currentPath.value === new URL(tab.href, window.location.origin).pathname
    : currentPath.value.startsWith(new URL(tab.href, window.location.origin).pathname);
</script>

<template>
    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-teal-100 bg-gradient-to-r from-white via-teal-50/40 to-blue-50/50 backdrop-blur-xl lg:hidden">
        <div class="flex min-h-16 items-center justify-around px-2 pt-1" :style="{ paddingBottom: 'env(safe-area-inset-bottom, 0px)' }">
            <Link
                v-for="tab in tabs"
                :key="tab.label"
                :href="tab.href"
                class="relative flex min-h-12 flex-1 flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-medium transition active:scale-95"
                :class="isActive(tab) ? 'font-semibold text-slate-800' : 'text-slate-400'"
            >
                <span class="relative flex h-8 w-10 items-center justify-center rounded-xl transition" :class="isActive(tab) ? `${tab.tone} shadow-sm ring-1 ring-black/5` : ''">
                    <component :is="tab.icon" class="h-[18px] w-[18px]" />
                    <span v-if="tab.badge > 0" class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white">
                        {{ tab.badge > 9 ? '9+' : tab.badge }}
                    </span>
                </span>
                {{ tab.label }}
            </Link>
            <button type="button" class="flex min-h-12 flex-1 flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-medium text-slate-400 transition active:scale-95" @click="emit('open-menu')">
                <span class="flex h-7 w-9 items-center justify-center"><Menu class="h-[18px] w-[18px]" /></span>
                Lagi
            </button>
        </div>
    </nav>
</template>
