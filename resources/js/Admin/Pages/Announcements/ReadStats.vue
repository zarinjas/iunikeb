<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Clock } from 'lucide-vue-next';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import PageHeader from '@/Shared/Components/PageHeader.vue';
import StatusBadge from '@/Shared/Components/StatusBadge.vue';
import { Button } from '@/Shared/Components/ui/button';

defineProps({
    announcement: { type: Object, required: true },
    users: { type: Object, required: true },
});
</script>

<template>
    <Head title="Jejak Bacaan" />

    <AdminLayout>
        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <Button :as="Link" :href="`/admin/announcements/${announcement.id}`" variant="outline" size="sm">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Kembali
                </Button>
            </div>

            <PageHeader
                :title="`Jejak Bacaan: ${announcement.title}`"
                :description="`Status bacaan untuk pengumuman yang dihantar kepada ahli tertentu. ${announcement.read_count} daripada ${announcement.targeted_count} telah dibaca.`"
            />

            <div v-if="users.data.length === 0" class="rounded-3xl border border-slate-200 bg-white p-12 text-center">
                <p class="text-sm text-slate-500">Pengumuman ini tidak dihantar kepada mana-mana ahli tertentu.</p>
            </div>

            <div v-else class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-left">
                            <th class="px-5 py-3 font-medium text-slate-500">Ahli</th>
                            <th class="px-5 py-3 font-medium text-slate-500">E-mel</th>
                            <th class="px-5 py-3 font-medium text-slate-500">Status</th>
                            <th class="px-5 py-3 font-medium text-slate-500">Tarikh Baca</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="border-b border-slate-50 last:border-0">
                            <td class="px-5 py-3 font-medium text-slate-950">{{ user.name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ user.email || '-' }}</td>
                            <td class="px-5 py-3">
                                <span v-if="user.is_read" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                    <CheckCircle2 class="h-3 w-3" />
                                    Sudah Baca
                                </span>
                                <span v-else class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">
                                    <Clock class="h-3 w-3" />
                                    Belum Baca
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ user.read_at || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="users.links?.length > 3" class="flex flex-wrap gap-2">
                <Button
                    v-for="link in users.links"
                    :key="`${link.label}-${link.url}`"
                    :as="link.url ? Link : 'button'"
                    :href="link.url || undefined"
                    :variant="link.active ? 'default' : 'outline'"
                    :disabled="!link.url"
                    v-html="link.label"
                />
            </div>
        </section>
    </AdminLayout>
</template>
