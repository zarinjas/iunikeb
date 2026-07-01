<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Building2, LockKeyhole, ShieldCheck, UsersRound } from 'lucide-vue-next';

const page = usePage();
const faviconUrl = computed(() => page.props.appSettings?.cooperative?.favicon_url);

const props = defineProps({
    variant: {
        type: String,
        default: 'member',
        validator: (value) => ['admin', 'member'].includes(value),
    },
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    cooperativeName: { type: String, default: '' },
    logoUrl: { type: String, default: null },
    primaryColor: { type: String, default: '#0F766E' },
});

const isAdmin = computed(() => props.variant === 'admin');
const areaLabel = computed(() => (isAdmin.value ? 'Portal Koperasi' : 'Portal Ahli'));
const showcaseTitle = computed(() => (
    isAdmin.value
        ? 'Satu akses untuk mengurus koperasi.'
        : 'Urus keahlian anda dengan lebih mudah.'
));
const showcaseDescription = computed(() => (
    isAdmin.value
        ? 'Akses ruang kerja anda dengan selamat, ringkas dan tersusun.'
        : 'Semak maklumat keahlian, pengumuman dan perkhidmatan koperasi pada bila-bila masa.'
));
</script>

<template>
    <Head>
        <link rel="icon" :href="faviconUrl || '/favicon.ico'" />
    </Head>

    <main
        class="flex min-h-screen items-center bg-slate-100 p-0 text-slate-950 sm:p-6 lg:p-8"
        :style="{ '--auth-primary': primaryColor }"
    >
        <div class="mx-auto grid min-h-screen w-full max-w-5xl overflow-hidden bg-white shadow-xl shadow-slate-900/10 sm:min-h-[680px] sm:rounded-[1.75rem] lg:min-h-0 lg:grid-cols-[0.9fr_1.1fr]">
            <section class="auth-showcase relative hidden min-h-[650px] overflow-hidden p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-12">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950/15 via-transparent to-slate-950/40" />
                <div class="relative z-10 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/20 bg-white/95 shadow-lg backdrop-blur">
                        <img v-if="logoUrl" :src="logoUrl" :alt="cooperativeName" class="h-9 w-9 object-contain" />
                        <Building2 v-else class="h-6 w-6" :style="{ color: primaryColor }" />
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-white/65">{{ areaLabel }}</p>
                        <p class="mt-0.5 font-semibold text-white">{{ cooperativeName || 'Portal Koperasi' }}</p>
                    </div>
                </div>

                <div class="relative z-10 max-w-md">
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-white/15 bg-white/10 backdrop-blur-md">
                        <ShieldCheck v-if="isAdmin" class="h-6 w-6" />
                        <UsersRound v-else class="h-6 w-6" />
                    </div>
                    <h1 class="text-3xl font-semibold leading-tight tracking-[-0.03em] xl:text-4xl">{{ showcaseTitle }}</h1>
                    <p class="mt-4 max-w-sm text-base leading-7 text-white/70">{{ showcaseDescription }}</p>
                </div>

                <div class="relative z-10 flex items-center gap-2 text-xs text-white/60">
                    <LockKeyhole class="h-3.5 w-3.5" />
                    <span>Sambungan selamat dan data peribadi dilindungi</span>
                </div>
            </section>

            <section class="relative flex items-center justify-center px-5 py-8 sm:px-10 lg:px-14 xl:px-16">
                <div class="absolute inset-x-0 top-0 h-1 lg:hidden" :style="{ backgroundColor: primaryColor }" />
                <div class="w-full max-w-md">
                    <div class="mb-8 flex items-center justify-between lg:hidden">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-50 ring-1 ring-slate-200">
                                <img v-if="logoUrl" :src="logoUrl" :alt="cooperativeName" class="h-8 w-8 object-contain" />
                                <Building2 v-else class="h-5 w-5" :style="{ color: primaryColor }" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">{{ areaLabel }}</p>
                                <p class="max-w-[220px] truncate text-sm font-semibold">{{ cooperativeName || 'Portal Koperasi' }}</p>
                            </div>
                        </div>
                    </div>

                    <Link href="/" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900">
                        <ArrowLeft class="h-4 w-4" />
                        Laman utama
                    </Link>

                    <div class="mb-7">
                        <p class="mb-2 text-sm font-semibold" :style="{ color: primaryColor }">Selamat kembali</p>
                        <h2 class="text-2xl font-semibold tracking-[-0.025em] text-slate-950 sm:text-3xl">{{ title }}</h2>
                        <p v-if="subtitle" class="mt-3 max-w-sm text-sm leading-6 text-slate-500">{{ subtitle }}</p>
                    </div>

                    <slot />
                    <slot name="quickLogin" />

                    <p class="mt-8 text-center text-xs leading-5 text-slate-400">
                        Dengan meneruskan, anda bersetuju mematuhi polisi keselamatan portal koperasi.
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>

<style scoped>
.auth-showcase {
    background-color: var(--auth-primary);
    background-image:
        radial-gradient(circle at 78% 16%, rgb(255 255 255 / 0.18), transparent 28%),
        radial-gradient(circle at 8% 88%, rgb(45 212 191 / 0.28), transparent 30%),
        linear-gradient(135deg, rgb(15 23 42 / 0.35), transparent 58%),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cg fill='none' stroke='%23fff' stroke-opacity='.055'%3E%3Cpath d='M0 40h160M0 80h160M0 120h160M40 0v160M80 0v160M120 0v160'/%3E%3Ccircle cx='80' cy='80' r='28'/%3E%3C/g%3E%3C/svg%3E");
}
</style>
