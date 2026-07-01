<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use App\Models\FrontpageSection;
use App\Models\FrontpageSectionItem;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class FrontpageDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::where('slug', 'koperasi-unikeb')->first();
        if (! $cooperative) return;

        $coopId = $cooperative->id;

        $this->seedHeroSection($coopId);
        $this->seedStatsSection($coopId);
        $this->seedServicesSection($coopId);
        $this->seedBenefitSection($coopId);
        $this->seedBusinessSection($coopId);
        $this->seedPromotionSection($coopId);
        $this->seedMembershipSection($coopId);
        $this->seedMemberPopupSection($coopId);
        $this->seedHeaderMenu($coopId);
    }

    private function section(int $coopId, string $key, string $title, ?string $subtitle = null): FrontpageSection
    {
        return FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => $key],
            ['title' => $title, 'subtitle' => $subtitle, 'is_active' => true]
        );
    }

    private function item(FrontpageSection $section, int $sortOrder, array $data): void
    {
        FrontpageSectionItem::firstOrCreate(
            ['section_id' => $section->id, 'sort_order' => $sortOrder],
            $data
        );
    }

    private function seedHeroSection(int $coopId): void
    {
        $section = $this->section($coopId, 'hero', 'Hero Slider');
        $this->item($section, 1, [
            'is_active' => true,
            'subtitle' => 'Koperasi Unikeb Berhad',
            'title' => 'Platform Digital Koperasi Moden',
            'description' => 'Urus keahlian, pengumuman, dokumen dan perkhidmatan koperasi dalam satu platform yang mudah dan pantas.',
            'button_text' => 'Ketahui Lebih Lanjut',
            'button_url' => '/tentang-kami',
        ]);
    }

    private function seedStatsSection(int $coopId): void
    {
        $section = $this->section($coopId, 'stats', 'Statistik');
        foreach ([
            1 => ['value' => '26,000+', 'title' => 'Keanggotaan Aktif', 'icon' => 'Users'],
            2 => ['value' => '7', 'title' => 'Perniagaan Milik Koperasi', 'icon' => 'Store'],
            3 => ['value' => 'RM34.7 Juta', 'title' => 'Jumlah Aset', 'icon' => 'TrendingUp'],
            4 => ['value' => 'Sejak 1996', 'title' => 'Ditubuhkan', 'icon' => 'CalendarDays'],
        ] as $order => $s) {
            $this->item($section, $order, $s);
        }
    }

    private function seedServicesSection(int $coopId): void
    {
        $section = $this->section($coopId, 'services', 'Perkhidmatan Koperasi', 'Akses perkhidmatan utama koperasi dengan lebih mudah.');
        foreach ([
            1 => ['title' => 'Pembiayaan Anggota', 'description' => 'Permohonan pembiayaan secara digital dengan proses yang pantas.', 'icon' => 'HandCoins'],
            2 => ['title' => 'Simpanan & Syer', 'description' => 'Urus simpanan dan syer koperasi dengan mudah.', 'icon' => 'Wallet'],
            3 => ['title' => 'Kebajikan Anggota', 'description' => 'Perlindungan dan manfaat tambahan untuk kebajikan ahli.', 'icon' => 'ShieldCheck'],
        ] as $order => $s) {
            $this->item($section, $order, $s);
        }
    }

    private function seedBenefitSection(int $coopId): void
    {
        $section = $this->section($coopId, 'benefit', 'Manfaat Ahli', 'Nikmati pelbagai manfaat sebagai ahli koperasi.');
        foreach ([
            1 => ['title' => 'Kuasa Membeli', 'description' => 'Beli barangan keperluan pada harga lebih rendah.', 'icon' => 'ShoppingBag'],
            2 => ['title' => 'Manfaat Kewangan', 'description' => 'Akses kepada pembiayaan dan simpanan eksklusif.', 'icon' => 'Banknote'],
            3 => ['title' => 'Peluang Perniagaan', 'description' => 'Sertai jaringan perniagaan koperasi.', 'icon' => 'Store'],
            4 => ['title' => 'Kebajikan Ahli', 'description' => 'Manfaat kebajikan dan perlindungan untuk ahli.', 'icon' => 'HeartHandshake'],
        ] as $order => $s) {
            $this->item($section, $order, $s);
        }
    }

    private function seedBusinessSection(int $coopId): void
    {
        $section = $this->section($coopId, 'business', 'Perniagaan Koperasi', 'Cabang perniagaan milik koperasi.');
        foreach ([
            1 => ['title' => 'Kedai Koperasi', 'description' => 'Peruncitan dan barangan keperluan.'],
            2 => ['title' => 'Hartanah & Sewaan', 'description' => 'Pelaburan hartanah strategik.'],
            3 => ['title' => 'Stesen Minyak', 'description' => 'Operasi stesen minyak.'],
            4 => ['title' => 'E-Dagang', 'description' => 'Platform jualan online.'],
            5 => ['title' => 'Bilik Seminar', 'description' => 'Sewaan ruang acara.'],
        ] as $order => $s) {
            $this->item($section, $order, $s);
        }
    }

    private function seedPromotionSection(int $coopId): void
    {
        $this->section($coopId, 'promotion', 'Promosi');
    }

    private function seedMembershipSection(int $coopId): void
    {
        $section = $this->section($coopId, 'membership', 'Sertai Koperasi Kami', 'Rebut peluang menjadi sebahagian daripada komuniti koperasi yang berkembang.');
        foreach ([
            1 => ['value' => '26,000+', 'title' => 'Keanggotaan Aktif'],
            2 => ['value' => 'RM34.7 Juta', 'title' => 'Jumlah Aset'],
            3 => ['value' => 'RM12.5 Juta', 'title' => 'Modal Syer'],
            4 => ['value' => 'RM18.2 Juta', 'title' => 'Simpanan'],
        ] as $order => $s) {
            $this->item($section, $order, $s);
        }
    }

    private function seedMemberPopupSection(int $coopId): void
    {
        $section = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'member_popup'],
            ['title' => 'Selamat Datang ke Portal Ahli!', 'is_active' => false]
        );
        $this->item($section, 1, [
            'is_active' => true,
            'description' => 'Nikmati pelbagai kemudahan digital koperasi melalui portal ahli. Kemaskini profil, mohon pembiayaan, dan urus keahlian anda.',
            'button_text' => 'Teruskan',
            'button_url' => '/member/dashboard',
        ]);
    }

    private function seedHeaderMenu(int $coopId): void
    {
        $items = [
            ['label' => 'Utama', 'url' => '/', 'sort_order' => 1],
            ['label' => 'Profil', 'url' => '/profil', 'sort_order' => 2],
            ['label' => 'Perkhidmatan', 'url' => '/perkhidmatan', 'sort_order' => 3],
            ['label' => 'Perniagaan', 'url' => '/perniagaan', 'sort_order' => 4],
            ['label' => 'Maklumat', 'url' => '/maklumat', 'sort_order' => 5],
            ['label' => 'Hubungi', 'url' => '/hubungi', 'sort_order' => 6],
        ];

        foreach ($items as $item) {
            Menu::firstOrCreate(
                ['cooperative_id' => $coopId, 'location' => 'header', 'url' => $item['url']],
                ['label' => $item['label'], 'sort_order' => $item['sort_order'], 'is_active' => true]
            );
        }
    }
}
