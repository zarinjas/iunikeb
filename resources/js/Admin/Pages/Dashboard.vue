<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { 
    FileCheck, MessagesSquare, Users, ClipboardCheck, HandCoins, 
    AlertTriangle, Plus, Activity, ArrowRight, Eye, CheckCircle 
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Bar, Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Tooltip,
    Legend,
    BarElement,
    BarController,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    LineController,
    Filler,
} from 'chart.js';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';

ChartJS.register(
    Tooltip,
    Legend,
    BarElement,
    BarController,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    LineController,
    Filler,
);

const props = defineProps({
    stats: { type: Array, default: () => [] },
    charts: { type: Object, required: true },
    actionRequired: { type: Object, default: () => ({ total: 0, items: [] }) },
    recentApplications: { type: Array, default: () => [] },
    recentComplaints: { type: Array, default: () => [] },
    recentActivities: { type: Array, default: () => [] },
});

const toneClasses = {
    info: 'bg-blue-50 text-blue-700',
    warning: 'bg-amber-50 text-amber-700',
    danger: 'bg-red-50 text-red-700',
    success: 'bg-emerald-50 text-emerald-700',
};

const unitBar = computed(() => ({
    labels: props.charts?.submissionsByUnit?.labels || [],
    datasets: [{
        label: 'Permohonan',
        data: props.charts?.submissionsByUnit?.data || [],
        backgroundColor: ['#0F766E', '#0D9488', '#14B8A6', '#2DD4BF', '#5EEAD4', '#99F6E4'],
        borderRadius: 6,
        borderSkipped: false,
        barThickness: 20,
    }],
}));

const membersLine = computed(() => ({
    labels: props.charts?.membersByMonth?.labels || [],
    datasets: [{
        label: 'Ahli Baharu',
        data: props.charts?.membersByMonth?.data || [],
        borderColor: '#0F766E',
        backgroundColor: 'rgba(15, 118, 110, 0.08)',
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#0F766E',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
    }],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
};

const barOptions = {
    ...chartOptions,
    indexAxis: 'y',
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 }, stepSize: 1 } },
        y: { grid: { display: false }, ticks: { font: { size: 11 } } },
    },
    plugins: { legend: { display: false } },
};

const lineOptions = {
    ...chartOptions,
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 }, stepSize: 1, callback: (v) => Number.isInteger(v) ? v : '' } },
    },
    plugins: { legend: { display: false } },
};
</script>

<template>
    <Head title="Papan Pemuka Admin" />

    <AdminLayout>
        <section class="space-y-6">
            <!-- Header & Quick Actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-normal text-slate-900">Papan Pemuka Admin</h1>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Tugasan yang memerlukan perhatian anda hari ini.
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link href="/admin/programs/create" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                        <Plus class="h-4 w-4" /> Program
                    </Link>
                    <Link href="/admin/announcements/create" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                        <Plus class="h-4 w-4" /> Pengumuman
                    </Link>
                </div>
            </div>

            <!-- Action Required Section -->
            <div v-if="actionRequired.total > 0" class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <AlertTriangle class="mt-0.5 h-5 w-5 text-amber-600" />
                    <div>
                        <h3 class="font-medium text-amber-900">Perhatian Diperlukan ({{ actionRequired.total }} Tindakan)</h3>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-amber-700">
                            <li v-for="(item, idx) in actionRequired.items" :key="idx">{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Smart KPI Cards -->
            <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                <article
                    v-for="card in stats"
                    :key="card.label"
                    class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl" :class="toneClasses[card.tone] || 'bg-teal-50 text-teal-700'">
                            <FileCheck v-if="card.icon === 'FileCheck'" class="h-5 w-5" />
                            <MessagesSquare v-else-if="card.icon === 'MessagesSquare'" class="h-5 w-5" />
                            <Users v-else-if="card.icon === 'Users'" class="h-5 w-5" />
                            <ClipboardCheck v-else-if="card.icon === 'ClipboardCheck'" class="h-5 w-5" />
                            <HandCoins v-else-if="card.icon === 'HandCoins'" class="h-5 w-5" />
                            <Users v-else class="h-5 w-5" />
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-medium text-slate-500">{{ card.label }}</p>
                    <div class="mt-1 flex items-baseline gap-2">
                        <p class="text-3xl font-semibold text-slate-950">{{ card.value }}</p>
                    </div>
                    <p v-if="card.suffix" class="mt-2 text-xs font-medium text-slate-500">
                        {{ card.suffix }}
                    </p>
                </article>
            </div>

            <!-- Operations Hub -->
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="xl:col-span-2 space-y-6">
                    
                    <!-- Recent Applications -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex justify-between items-center">
                            <div>
                                <h2 class="text-base font-semibold text-slate-950">Menanti Kelulusan (Terbaru)</h2>
                                <p class="text-sm text-slate-500">Permohonan yang memerlukan semakan segera.</p>
                            </div>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div v-if="recentApplications.length === 0" class="px-6 py-8 text-center text-sm text-slate-500">
                                Tiada permohonan menanti kelulusan.
                            </div>
                            <div v-for="(app, index) in recentApplications" :key="'app-'+index" class="flex items-center justify-between px-6 py-4 hover:bg-slate-50">
                                <div>
                                    <p class="font-medium text-slate-900">{{ app.name }}</p>
                                    <div class="mt-1 flex items-center gap-2 text-sm text-slate-500">
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">{{ app.type }}</span>
                                        <span>•</span>
                                        <span>{{ app.date }}</span>
                                    </div>
                                </div>
                                <div>
                                    <Link :href="app.url" class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                                        Semak <ArrowRight class="h-3.5 w-3.5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Complaints -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex justify-between items-center">
                            <div>
                                <h2 class="text-base font-semibold text-slate-950">Aduan Terkini</h2>
                                <p class="text-sm text-slate-500">Aduan dan cadangan yang masih terbuka.</p>
                            </div>
                            <Link href="/admin/complaints" class="text-sm font-medium text-teal-600 hover:text-teal-700">Lihat Semua</Link>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div v-if="recentComplaints.length === 0" class="px-6 py-8 text-center text-sm text-slate-500">
                                Tiada aduan terbuka.
                            </div>
                            <div v-for="(complaint, index) in recentComplaints" :key="'comp-'+index" class="flex items-center justify-between px-6 py-4 hover:bg-slate-50">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-semibold text-slate-500">{{ complaint.ticket_no }}</span>
                                        <p class="font-medium text-slate-900">{{ complaint.subject }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">{{ complaint.date }}</p>
                                </div>
                                <div>
                                    <Link :href="complaint.url" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                                        <Eye class="h-3.5 w-3.5" /> Jawab
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- System Context Sidebar -->
                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                        <h2 class="text-base font-semibold text-slate-950 flex items-center gap-2">
                            <Activity class="h-4 w-4 text-slate-400" /> Aktiviti Terkini
                        </h2>
                        <div class="mt-6 flow-root">
                            <ul role="list" class="-mb-8">
                                <li v-if="recentActivities.length === 0" class="pb-8 text-sm text-slate-500 text-center">
                                    Tiada aktiviti sistem.
                                </li>
                                <li v-for="(activity, idx) in recentActivities" :key="'act-'+idx">
                                    <div class="relative pb-8">
                                        <span v-if="idx !== recentActivities.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-100" aria-hidden="true" />
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center ring-8 ring-white">
                                                    <CheckCircle class="h-4 w-4 text-slate-500" />
                                                </span>
                                            </div>
                                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-slate-600">
                                                        <span class="font-medium text-slate-900">{{ activity.actor }}</span> {{ activity.action }}
                                                    </p>
                                                </div>
                                                <div class="whitespace-nowrap text-right text-xs text-slate-500">
                                                    <time :datetime="activity.date">{{ activity.date }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics (Deprioritized) -->
            <details class="group rounded-2xl border border-slate-200 bg-white shadow-sm [&_summary::-webkit-details-marker]:hidden">
                <summary class="flex cursor-pointer items-center justify-between px-6 py-4 font-semibold text-slate-900">
                    <span class="flex items-center gap-2">Analitik & Statistik</span>
                    <span class="transition group-open:rotate-180">
                        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                </summary>
                <div class="border-t border-slate-200 p-6">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="overflow-hidden rounded-xl border border-slate-100 bg-slate-50 p-6">
                            <h3 class="text-sm font-semibold text-slate-900">Permohonan Mengikut Unit</h3>
                            <div v-if="charts.submissionsByUnit?.labels?.length" class="mt-4 overflow-hidden" :style="{ height: Math.max(160, (charts.submissionsByUnit?.labels?.length ?? 0) * 48) + 'px' }">
                                <Bar :data="unitBar" :options="barOptions" />
                            </div>
                            <p v-else class="mt-6 text-center text-sm text-slate-400">Tiada data permohonan.</p>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-slate-100 bg-slate-50 p-6">
                            <h3 class="text-sm font-semibold text-slate-900">Ahli Baharu Bulanan</h3>
                            <div class="mt-4 h-64 overflow-hidden">
                                <Line :data="membersLine" :options="lineOptions" />
                            </div>
                        </div>
                    </div>
                </div>
            </details>
            
        </section>
    </AdminLayout>
</template>