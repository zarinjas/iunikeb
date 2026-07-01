<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import FlashToast from '@/Shared/Components/FlashToast.vue';
import PublicFooter from '@/Shared/Components/PublicFooter.vue';
import PublicNavbar from '@/Shared/Components/PublicNavbar.vue';

const page = usePage();

const headerMenus = computed(() => page.props.publicMenus?.header ?? []);
const footerMenus = computed(() => page.props.publicMenus?.footer ?? {});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-blue-100/40 via-white to-sky-100/25 text-slate-950">
        <PublicNavbar :menus="headerMenus" variant="solid" />

        <slot />

        <PublicFooter :footer-menus="footerMenus" />
    </div>

    <FlashToast />
</template>
