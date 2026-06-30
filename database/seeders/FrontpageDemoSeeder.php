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

        // Hero
        $hero = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'hero'],
            ['title' => 'Hero Slider', 'is_active' => true]
        );
        $hero->allItems()->delete();
        FrontpageSectionItem::create([
            'section_id' => $hero->id, 'sort_order' => 1, 'is_active' => true,
            'subtitle' => 'Koperasi Unikeb Berhad',
            'title' => 'Platform Digital Koperasi Moden',
            'description' => 'Urus keahlian, pengumuman, dokumen dan perkhidmatan koperasi dalam satu platform yang mudah dan pantas.',
            'button_text' => 'Ketahui Lebih Lanjut',
            'button_url' => '/tentang-kami',
        ]);

        // Stats
        $stats = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'stats'],
            ['title' => 'Statistik', 'is_active' => true]
        );
        $stats->allItems()->delete();
        foreach ([
            ['value' => '26,000+', 'title' => 'Keanggotaan Aktif', 'icon' => 'Users'],
            ['value' => '7', 'title' => 'Perniagaan Milik Koperasi', 'icon' => 'Store'],
            ['value' => 'RM34.7 Juta', 'title' => 'Jumlah Aset', 'icon' => 'TrendingUp'],
            ['value' => 'Sejak 1996', 'title' => 'Ditubuhkan', 'icon' => 'CalendarDays'],
        ] as $i => $s) {
            FrontpageSectionItem::create([
                'section_id' => $stats->id, 'sort_order' => $i + 1, 'is_active' => true,
                'value' => $s['value'], 'title' => $s['title'], 'icon' => $s['icon'],
            ]);
        }

        // Services
        $services = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'services'],
            ['title' => 'Perkhidmatan Koperasi', 'subtitle' => 'Akses perkhidmatan utama koperasi dengan lebih mudah.', 'is_active' => true]
        );
        $services->allItems()->delete();
        foreach ([
            ['title' => 'Pembiayaan Anggota', 'description' => 'Permohonan pembiayaan secara digital dengan proses yang pantas.', 'icon' => 'HandCoins'],
            ['title' => 'Simpanan & Syer', 'description' => 'Urus simpanan dan syer koperasi dengan mudah.', 'icon' => 'Wallet'],
            ['title' => 'Kebajikan Anggota', 'description' => 'Perlindungan dan manfaat tambahan untuk kebajikan ahli.', 'icon' => 'ShieldCheck'],
        ] as $i => $s) {
            FrontpageSectionItem::create([
                'section_id' => $services->id, 'sort_order' => $i + 1, 'is_active' => true,
                'title' => $s['title'], 'description' => $s['description'], 'icon' => $s['icon'],
            ]);
        }

        // Benefit
        $benefit = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'benefit'],
            ['title' => 'Manfaat Ahli', 'subtitle' => 'Nikmati pelbagai manfaat sebagai ahli koperasi.', 'is_active' => true]
        );
        $benefit->allItems()->delete();
        foreach ([
            ['title' => 'Kuasa Membeli', 'description' => 'Beli barangan keperluan pada harga lebih rendah.', 'icon' => 'ShoppingBag'],
            ['title' => 'Manfaat Kewangan', 'description' => 'Akses kepada pembiayaan dan simpanan eksklusif.', 'icon' => 'Banknote'],
            ['title' => 'Peluang Perniagaan', 'description' => 'Sertai jaringan perniagaan koperasi.', 'icon' => 'Store'],
            ['title' => 'Kebajikan Ahli', 'description' => 'Manfaat kebajikan dan perlindungan untuk ahli.', 'icon' => 'HeartHandshake'],
        ] as $i => $s) {
            FrontpageSectionItem::create([
                'section_id' => $benefit->id, 'sort_order' => $i + 1, 'is_active' => true,
                'title' => $s['title'], 'description' => $s['description'], 'icon' => $s['icon'],
            ]);
        }

        // Business
        $business = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'business'],
            ['title' => 'Perniagaan Koperasi', 'subtitle' => 'Cabang perniagaan milik koperasi.', 'is_active' => true]
        );
        $business->allItems()->delete();
        foreach ([
            ['title' => 'Kedai Koperasi', 'description' => 'Peruncitan dan barangan keperluan.'],
            ['title' => 'Hartanah & Sewaan', 'description' => 'Pelaburan hartanah strategik.'],
            ['title' => 'Stesen Minyak', 'description' => 'Operasi stesen minyak.'],
            ['title' => 'E-Dagang', 'description' => 'Platform jualan online.'],
            ['title' => 'Bilik Seminar', 'description' => 'Sewaan ruang acara.'],
        ] as $i => $s) {
            FrontpageSectionItem::create([
                'section_id' => $business->id, 'sort_order' => $i + 1, 'is_active' => true,
                'title' => $s['title'], 'description' => $s['description'],
            ]);
        }

        // Promotion
        $promotion = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'promotion'],
            ['title' => 'Promosi', 'is_active' => true]
        );
        // Items for promotion can be managed via admin

        // Membership
        $membership = FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'membership'],
            ['title' => 'Sertai Koperasi Kami', 'subtitle' => 'Rebut peluang menjadi sebahagian daripada komuniti koperasi yang berkembang.', 'is_active' => true]
        );
        $membership->allItems()->delete();
        foreach ([
            ['value' => '26,000+', 'title' => 'Keanggotaan Aktif'],
            ['value' => 'RM34.7 Juta', 'title' => 'Jumlah Aset'],
            ['value' => 'RM12.5 Juta', 'title' => 'Modal Syer'],
            ['value' => 'RM18.2 Juta', 'title' => 'Simpanan'],
        ] as $i => $s) {
            FrontpageSectionItem::create([
                'section_id' => $membership->id, 'sort_order' => $i + 1, 'is_active' => true,
                'value' => $s['value'], 'title' => $s['title'],
            ]);
        }

        // Footer
        FrontpageSection::updateOrCreate(
            ['cooperative_id' => $coopId, 'key' => 'footer'],
            ['title' => 'Footer', 'is_active' => true]
        );

        // Header Menu
        Menu::where('cooperative_id', $coopId)->delete();
        $menuItems = [
            ['location' => 'header', 'label' => 'Utama', 'url' => '/', 'sort_order' => 1],
            ['location' => 'header', 'label' => 'Profil', 'url' => '/profil', 'sort_order' => 2],
            ['location' => 'header', 'label' => 'Perkhidmatan', 'url' => '/perkhidmatan', 'sort_order' => 3],
            ['location' => 'header', 'label' => 'Perniagaan', 'url' => '/perniagaan', 'sort_order' => 4],
            ['location' => 'header', 'label' => 'Maklumat', 'url' => '/maklumat', 'sort_order' => 5],
            ['location' => 'header', 'label' => 'Hubungi', 'url' => '/hubungi', 'sort_order' => 6],
        ];
        foreach ($menuItems as $item) {
            Menu::create(array_merge($item, ['cooperative_id' => $coopId, 'is_active' => true]));
        }
    }
}
