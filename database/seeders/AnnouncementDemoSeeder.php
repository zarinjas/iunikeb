<?php

namespace Database\Seeders;

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AnnouncementDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::query()->where('slug', 'koperasi-unikeb')->first();
        if (! $cooperative) {
            return;
        }

        $adminId = User::query()->where('email', 'admin@iunikeb.com.my')->value('id');

        $storage = Storage::disk('public');
        $storage->makeDirectory('announcements');

        $announcements = [
            [
                'title' => 'Pembukaan Permohonan Keanggotaan Sesi 2026',
                'summary' => 'Permohonan keanggotaan baharu kini dibuka untuk sesi 2026.',
                'content' => "Kami dengan sukacitanya memaklumkan bahawa permohonan keanggotaan baharu Koperasi Unikeb bagi sesi 2026 kini telah dibuka.\n\nSila lengkapkan borang permohonan secara online melalui portal rasmi koperasi. Permohonan akan diproses dalam tempoh 14 hari bekerja.\n\nSyarat-syarat kelayakan:\n- Warganegara Malaysia berumur 18 tahun ke atas\n- Berpendapatan tetap\n- Berminat dengan konsep koperasi",
                'audience' => AnnouncementAudience::Public,
                'status' => AnnouncementStatus::Published,
                'priority' => 'penting',
                'is_pinned' => true,
                'published_at' => now()->subDays(5),
                'expires_at' => now()->addMonths(3),
                'image' => true, 'color' => '#2563eb',
            ],
            [
                'title' => 'Notis Kemaskini Maklumat Anggota',
                'summary' => 'Anggota digalakkan menyemak dan mengemaskini maklumat peribadi melalui portal ahli.',
                'content' => "Semua anggota Koperasi Unikeb diminta untuk menyemak dan mengemaskini maklumat peribadi masing-masing melalui portal ahli.\n\nMaklumat yang perlu dikemaskini termasuk:\n- Alamat terkini\n- No. telefon bimbit\n- Alamat emel\n- Maklumat pasangan/waris\n- Maklumat pekerjaan\n\nSila log masuk ke portal ahli untuk kemaskini.",
                'audience' => AnnouncementAudience::Members,
                'status' => AnnouncementStatus::Published,
                'is_pinned' => true,
                'published_at' => now()->subDays(3),
                'image' => false,
            ],
            [
                'title' => 'Hebahan Jadual Mesyuarat Agung Tahunan 2026',
                'summary' => 'Jadual dan agenda Mesyuarat Agung Tahunan 2026 telah ditetapkan.',
                'content' => "Mesyuarat Agung Tahunan (MAT) Koperasi Unikeb bagi tahun kewangan 2025 akan diadakan seperti berikut:\n\nTarikh: 15 Ogos 2026 (Sabtu)\nMasa: 8:30 pagi - 1:30 petang\nTempat: Dewan Serbaguna Koperasi, Lot 1234, Jalan Koperasi\n\nAgenda:\n- Pembentangan Laporan Tahunan\n- Pengesahan Penyata Kewangan\n- Pelantikan Juruaudit\n- Pembahagian Dividen\n- Pemilihan Ahli Lembaga\n\nSemua anggota dijemput hadir.",
                'audience' => AnnouncementAudience::Public,
                'status' => AnnouncementStatus::Published,
                'priority' => 'penting',
                'is_pinned' => false,
                'published_at' => now()->subDays(10),
                'image' => true, 'color' => '#7c3aed',
            ],
            [
                'title' => 'Makluman Waktu Operasi Kaunter Hari Raya',
                'summary' => 'Sila rujuk waktu operasi kaunter yang telah diubah sempena cuti hari raya.',
                'content' => "Dimaklumkan bahawa waktu operasi kaunter koperasi akan berubah sempena cuti Hari Raya Aidilfitri seperti berikut:\n\n28-30 April: TUTUP (Hari Raya)\n1-2 Mei: Operasi 9:00 pagi - 1:00 tengahari\n3 Mei: Operasi seperti biasa\n\nKaunter akan beroperasi seperti biasa selepas cuti.",
                'audience' => AnnouncementAudience::Public,
                'status' => AnnouncementStatus::Published,
                'is_pinned' => false,
                'published_at' => now()->subDays(15),
                'expires_at' => now()->addDays(20),
                'image' => false,
            ],
            [
                'title' => 'Pelancaran Kemudahan Ansuran Mudah',
                'summary' => 'Koperasi kini memperkenalkan kemudahan ansuran mudah untuk ahli.',
                'content' => "Koperasi Unikeb dengan sukacitanya melancarkan kemudahan Ansuran Mudah untuk semua anggota!\n\nDengan Ansuran Mudah, anda boleh membeli pelbagai produk elektronik, perabot dan perkakas rumah secara ansuran tanpa perlu ke bank.\n\nAntara kelebihan:\n- Proses cepat dan mudah\n- Kadar keuntungan rendah (serendah 0% untuk 6 bulan)\n- Pelbagai pilihan produk\n- Bayaran bulanan mengikut kemampuan\n\nKunjungi portal ahli untuk maklumat lanjut.",
                'audience' => AnnouncementAudience::Members,
                'status' => AnnouncementStatus::Published,
                'is_pinned' => false,
                'published_at' => now()->subDays(7),
                'image' => true, 'color' => '#059669',
            ],
            [
                'title' => 'Notis Dalaman: Latihan Staf Bulanan',
                'summary' => 'Latihan staf bulanan akan diadakan seperti dijadualkan.',
                'content' => "Semua staf diingatkan bahawa sesi latihan bulanan akan diadakan pada:\n\nTarikh: 20 haribulan setiap bulan\nMasa: 2:30 petang - 4:30 petang\nTempat: Bilik Mesyuarat Utama\n\nKehadiran adalah wajib.",
                'audience' => AnnouncementAudience::Admins,
                'status' => AnnouncementStatus::Published,
                'is_pinned' => false,
                'published_at' => now()->subDays(2),
                'image' => false,
            ],
            [
                'title' => 'Tawaran Promosi: Pembiayaan Tanpa Caj Proses',
                'summary' => 'Promosi terhad! Pembiayaan tanpa caj proses untuk permohonan sebelum 31 Julai.',
                'content' => "Jangan lepaskan peluang! Sebagai menghargai anggota setia, Koperasi Unikeb menawarkan promosi pembiayaan tanpa caj proses.\n\nTempoh promosi: Sehingga 31 Julai 2026\n\nTermasuk:\n- Pembiayaan Peribadi\n- Pembiayaan Kenderaan\n- Pembiayaan Pendidikan\n\nHubungi pegawai kami untuk keterangan lanjut.",
                'audience' => AnnouncementAudience::Members,
                'status' => AnnouncementStatus::Published,
                'is_pinned' => false,
                'published_at' => now()->subDay(),
                'expires_at' => now()->addMonths(1),
                'image' => true, 'color' => '#dc2626',
            ],
            [
                'title' => 'Makluman: Sistem Dalam Penyelenggaraan Hujung Minggu',
                'summary' => 'Sistem portal akan menjalani penyelenggaraan pada hujung minggu ini.',
                'content' => "Dimaklumkan bahawa sistem portal koperasi akan menjalani penyelenggaraan berkala pada:\n\nMula: Sabtu, 8:00 malam\nSelesai: Ahad, 8:00 pagi\n\nSemua perkhidmatan online mungkin tidak dapat diakses seketika. Mohon maaf atas kesulitan ini.",
                'audience' => AnnouncementAudience::Members,
                'status' => AnnouncementStatus::Published,
                'priority' => 'segera',
                'is_pinned' => false,
                'published_at' => now()->subDays(20),
                'expires_at' => now()->subDays(18),
                'image' => false,
            ],
        ];

        foreach ($announcements as $data) {
            $slug = str($data['title'])->slug();
            $imagePath = null;

            if ($data['image'] ?? false) {
                $imagePath = "announcements/{$slug}.svg";
                if (! $storage->exists($imagePath)) {
                    $storage->put($imagePath, $this->announcementSvg($data['title'], $data['color'] ?? '#6366f1'));
                }
            }

            Announcement::updateOrCreate(
                ['cooperative_id' => $cooperative->id, 'slug' => $slug],
                [
                    'title' => $data['title'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                    'image_path' => $imagePath,
                    'audience' => $data['audience']->value,
                    'status' => $data['status']->value,
                    'priority' => $data['priority'] ?? 'normal',
                    'is_pinned' => $data['is_pinned'],
                    'send_via' => $data['send_via'] ?? 'in_app',
                    'published_at' => $data['published_at'],
                    'expires_at' => $data['expires_at'] ?? null,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }
    }

    private function announcementSvg(string $title, string $color): string
    {
        $escaped = htmlspecialchars($title, ENT_XML1);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="600" viewBox="0 0 1200 600">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$color};stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#1e1b4b;stop-opacity:1"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="600" fill="url(#g)"/>
  <text x="600" y="280" text-anchor="middle" font-family="system-ui, sans-serif" font-size="48" font-weight="bold" fill="white">{$escaped}</text>
  <rect x="450" y="320" width="300" height="4" rx="2" fill="white" opacity="0.3"/>
  <text x="600" y="370" text-anchor="middle" font-family="system-ui, sans-serif" font-size="24" fill="white" opacity="0.7">Koperasi Unikeb</text>
</svg>
SVG;
    }
}
