<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { Building2, FileText, Mail, MapPin, Phone } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/Shared/Components/AppLogo.vue';

const page = usePage();

const props = defineProps({
    footerMenus: {
        type: Object,
        default: () => ({}),
    },
});

const cooperative = computed(() => page.props.appSettings?.cooperative ?? {});
const contact = computed(() => page.props.appSettings?.contact ?? {});

const defaultFooterGroups = [
    {
        title: 'Halaman',
        links: [
            { label: 'Utama', url: '/' },
            { label: 'Pengumuman', url: '/pengumuman' },
            { label: 'Borang Online', url: '/forms' },
            { label: 'Ansuran Mudah', url: '/ansuran' },
        ],
    },
    {
        title: 'Keahlian',
        links: [
            { label: 'Permohonan Ahli', url: '/membership/apply' },
            { label: 'Semakan Ahli', url: '/verify/member/' },
        ],
    },
    {
        title: 'Log Masuk',
        links: [
            { label: 'Portal Ahli', url: '/member/login' },
            { label: 'Admin', url: '/admin/login' },
        ],
    },
];

const footerGroups = computed(() => {
    const groups = [];
    const footerLocations = ['footer_koperasi', 'footer_perkhidmatan', 'footer_perniagaan', 'footer_sumber'];

    const hasCmsMenus = footerLocations.some(loc => (props.footerMenus?.[loc]?.length ?? 0) > 0);

    if (hasCmsMenus) {
        const groupConfigs = [
            { key: 'footer_koperasi', title: 'Koperasi' },
            { key: 'footer_perkhidmatan', title: 'Perkhidmatan' },
            { key: 'footer_perniagaan', title: 'Perniagaan' },
            { key: 'footer_sumber', title: 'Sumber' },
        ];
        for (const cfg of groupConfigs) {
            const items = (props.footerMenus?.[cfg.key] || []).map(link => ({
                label: link.label,
                url: link.url || '#',
            }));
            if (items.length) {
                groups.push({ title: cfg.title, links: items });
            }
        }
    }

    return groups.length ? groups : defaultFooterGroups;
});

const footerText = computed(
    () => cooperative.value.footer_text || `${cooperative.value.name || 'Koperasi ini'} menyediakan platform maklumat, perkhidmatan dan sokongan anggota secara lebih tersusun.`,
);

const address = computed(() => [
    contact.value.address_line_1,
    contact.value.address_line_2,
    [contact.value.postcode, contact.value.city, contact.value.state].filter(Boolean).join(' '),
].filter(Boolean).join(', '));
</script>

<template>
    <footer class="relative overflow-hidden border-t border-teal-900/20 bg-slate-950 text-slate-100">
        <div class="absolute inset-x-0 h-24 bg-gradient-to-b from-teal-400/10 to-transparent" />
        <div class="relative mx-auto grid w-full max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.15fr_1.85fr] lg:px-8">
            <div class="space-y-6">
                <AppLogo
                    :name="cooperative.name"
                    :logo-url="cooperative.logo_url"
                    href="/"
                    size="sm"
                />
                <p class="max-w-md text-sm leading-7 text-slate-300">
                    {{ footerText }}
                </p>
                <div class="grid gap-3 text-sm text-slate-300">
                    <div class="flex items-start gap-3">
                        <MapPin class="mt-1 h-4 w-4 shrink-0 text-teal-300" />
                        <span>{{ address || 'Alamat koperasi akan dikemas kini.' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Phone class="h-4 w-4 shrink-0 text-teal-300" />
                        <span>{{ contact.phone || 'Maklumat telefon akan dikemas kini' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Mail class="h-4 w-4 shrink-0 text-teal-300" />
                        <span>{{ contact.email || 'Maklumat e-mel akan dikemas kini' }}</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="group in footerGroups" :key="group.title">
                    <div class="flex items-center gap-2 text-sm font-semibold text-white">
                        <FileText class="h-4 w-4 text-teal-300" />
                        <span>{{ group.title }}</span>
                    </div>
                    <div class="mt-4 grid gap-3">
                        <Link
                            v-for="item in group.links"
                            :key="item.url"
                            :href="item.url"
                            class="text-sm text-slate-300 transition-colors hover:text-white"
                        >
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-4 py-5 text-sm text-slate-400 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <span>© {{ new Date().getFullYear() }} {{ cooperative.name }}</span>
                <div class="flex items-center gap-2 text-slate-400">
                    <Building2 class="h-4 w-4" />
                    <span>Laman awam berasaskan CMS</span>
                </div>
            </div>
        </div>
    </footer>
</template>
