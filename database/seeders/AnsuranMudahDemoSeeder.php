<?php

namespace Database\Seeders;

use App\Models\AnsuranAgreementTemplate;
use App\Models\AnsuranCategory;
use App\Models\AnsuranProduct;
use App\Models\AnsuranProductImage;
use App\Models\AnsuranProductVariant;
use App\Models\AnsuranTenureOption;
use App\Models\Cooperative;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AnsuranMudahDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::first();
        if (! $cooperative) {
            $cooperative = Cooperative::create([
                'name' => 'Koperasi Unikeb',
                'short_name' => 'KDemo',
                'registration_no' => 'KO-2025-0001',
                'slug' => 'koperasi-demo',
                'status' => 'active',
            ]);
        }

        $storage = Storage::disk('public');
        $storage->makeDirectory('ansuran');

        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'desc' => 'Produk elektronik seperti TV, peti sejuk, mesin basuh dan lain-lain.', 'color' => '#2563eb'],
            ['name' => 'Perabot', 'slug' => 'perabot', 'desc' => 'Perabot rumah seperti sofa, katil, almari dan lain-lain.', 'color' => '#7c3aed'],
            ['name' => 'Perkakas Rumah', 'slug' => 'perkakas-rumah', 'desc' => 'Perkakas rumah seperti periuk, blender, vacum dan lain-lain.', 'color' => '#059669'],
        ];

        $categoryModels = [];
        foreach ($categories as $c) {
            $categoryModels[$c['slug']] = AnsuranCategory::updateOrCreate(
                ['cooperative_id' => $cooperative->id, 'slug' => $c['slug']],
                [
                    'name' => $c['name'],
                    'description' => $c['desc'],
                    'image_path' => $this->catImage($storage, $c['slug'], $c['name'], $c['color']),
                    'is_active' => true,
                ]
            );
        }

        $cat = fn($s) => $categoryModels[$s]->id;

        $this->seedElectronics($cooperative->id, $cat('elektronik'), $storage);
        $this->seedFurniture($cooperative->id, $cat('perabot'), $storage);
        $this->seedAppliances($cooperative->id, $cat('perkakas-rumah'), $storage);

        $tenureOptions = [
            ['months' => 3, 'interest_rate_percent' => 0, 'label' => '3 Bulan'],
            ['months' => 6, 'interest_rate_percent' => 0, 'label' => '6 Bulan'],
            ['months' => 9, 'interest_rate_percent' => 2.00, 'label' => '9 Bulan'],
            ['months' => 12, 'interest_rate_percent' => 2.00, 'label' => '12 Bulan'],
            ['months' => 18, 'interest_rate_percent' => 3.00, 'label' => '18 Bulan'],
            ['months' => 24, 'interest_rate_percent' => 4.00, 'label' => '24 Bulan'],
            ['months' => 36, 'interest_rate_percent' => 6.00, 'label' => '36 Bulan'],
        ];

        foreach ($tenureOptions as $t) {
            AnsuranTenureOption::updateOrCreate(
                ['cooperative_id' => $cooperative->id, 'months' => $t['months']],
                [
                    'interest_rate_percent' => $t['interest_rate_percent'],
                    'label' => $t['label'],
                    'is_active' => true,
                ]
            );
        }

        AnsuranAgreementTemplate::updateOrCreate(
            ['cooperative_id' => $cooperative->id, 'name' => 'Perjanjian Ansuran Mudah Standard'],
            [
                'description' => 'Template standard untuk semua permohonan ansuran mudah.',
                'content' => $this->agreementTemplate(),
                'is_active' => true,
            ]
        );
    }

    private function seedElectronics(int $coopId, int $catId, $storage): void
    {
        $items = [
            [
                'name' => 'TV Samsung U80000',
                'desc' => '<p>TV Samsung U80000 dengan teknologi Crystal UHD 4K.</p><ul><li>Crystal Processor 4K</li><li>HDR10+</li><li>Smart TV dengan Tizen OS</li><li>Dolby Digital Plus</li><li>Motion Xcelerator</li></ul>',
                'dp' => 20, 'guar' => 1, 'color' => '#1d4ed8',
                'variants' => [
                    ['name' => '48 inci', 'sku' => 'TV-SAM-U80000-48', 'price' => 1200, 'stock' => 10, 'attrs' => ['Saiz Skrin' => '48"', 'Resolusi' => '4K UHD']],
                    ['name' => '55 inci', 'sku' => 'TV-SAM-U80000-55', 'price' => 1600, 'stock' => 5, 'attrs' => ['Saiz Skrin' => '55"', 'Resolusi' => '4K UHD']],
                    ['name' => '65 inci', 'sku' => 'TV-SAM-U80000-65', 'price' => 2200, 'stock' => 3, 'attrs' => ['Saiz Skrin' => '65"', 'Resolusi' => '4K UHD']],
                ],
            ],
            [
                'name' => 'Peti Sejuk Panasonic Inverter',
                'desc' => '<p>Peti sejuk Panasonic 2 pintu dengan teknologi Inverter yang menjimatkan tenaga.</p><ul><li>Smart Inverter Compressor</li><li>Ag Clean Filter</li><li>LED Lighting</li><li>Penggunaan kuasa rendah</li></ul>',
                'dp' => 20, 'guar' => 1, 'color' => '#0284c7',
                'variants' => [
                    ['name' => '430L', 'sku' => 'FRIDGE-PAN-430', 'price' => 1500, 'stock' => 4, 'attrs' => ['Kapasiti' => '430L', 'Pintu' => '2 Pintu']],
                    ['name' => '530L', 'sku' => 'FRIDGE-PAN-530', 'price' => 2100, 'stock' => 2, 'attrs' => ['Kapasiti' => '530L', 'Pintu' => '2 Pintu']],
                ],
            ],
            [
                'name' => 'Mesin Basuh Samsung',
                'desc' => '<p>Mesin basuh automatik Samsung dengan pelbagai program cucian.</p><ul><li>Digital Inverter Technology</li><li>Eco Bubble</li><li>Quick Wash 30 minit</li><li>Child Lock</li></ul>',
                'dp' => 15, 'guar' => 1, 'color' => '#0369a1',
                'variants' => [
                    ['name' => '7kg', 'sku' => 'WASH-SAM-7', 'price' => 1100, 'stock' => 6, 'attrs' => ['Kapasiti' => '7kg', 'Jenis' => 'Front Loading']],
                    ['name' => '9kg', 'sku' => 'WASH-SAM-9', 'price' => 1500, 'stock' => 4, 'attrs' => ['Kapasiti' => '9kg', 'Jenis' => 'Front Loading']],
                    ['name' => '12kg', 'sku' => 'WASH-SAM-12', 'price' => 2000, 'stock' => 2, 'attrs' => ['Kapasiti' => '12kg', 'Jenis' => 'Front Loading']],
                ],
            ],
            [
                'name' => 'Air Conditioner Daikin',
                'desc' => '<p>Penyaman udara Daikin dengan teknologi Inverter untuk penjimatan maksimum.</p><ul><li>Daikin Inverter Technology</li><li>Coanda Airflow</li><li>Anti-corrosion Fin</li><li>Power Chill</li></ul>',
                'dp' => 20, 'guar' => 1, 'color' => '#0e7490',
                'variants' => [
                    ['name' => '1.0HP', 'sku' => 'AC-DAIKIN-10', 'price' => 1300, 'stock' => 8, 'attrs' => ['Kuasa' => '1.0HP', 'Jenis' => 'Non-Inverter']],
                    ['name' => '1.5HP', 'sku' => 'AC-DAIKIN-15', 'price' => 1800, 'stock' => 5, 'attrs' => ['Kuasa' => '1.5HP', 'Jenis' => 'Non-Inverter']],
                    ['name' => '2.0HP', 'sku' => 'AC-DAIKIN-20', 'price' => 2500, 'stock' => 3, 'attrs' => ['Kuasa' => '2.0HP', 'Jenis' => 'Non-Inverter']],
                ],
            ],
            [
                'name' => 'Peti Sejuk Sharp',
                'desc' => '<p>Peti sejuk Sharp dengan teknologi nanoplate untuk kesegaran makanan.</p><ul><li>Nanoplate Technology</li><li>Big Veggie Box</li><li>Energy Saving</li><li>Quick Cooling</li></ul>',
                'dp' => 15, 'guar' => 0, 'color' => '#075985',
                'variants' => [
                    ['name' => '320L', 'sku' => 'FRIDGE-SHARP-320', 'price' => 1100, 'stock' => 5, 'attrs' => ['Kapasiti' => '320L', 'Pintu' => '2 Pintu']],
                    ['name' => '420L', 'sku' => 'FRIDGE-SHARP-420', 'price' => 1600, 'stock' => 3, 'attrs' => ['Kapasiti' => '420L', 'Pintu' => '2 Pintu']],
                ],
            ],
            [
                'name' => 'Microwave Panasonic',
                'desc' => '<p>Ketuhar gelombang mikro Panasonic dengan fungsi pelbagai.</p><ul><li>Inverter Technology</li><li>Turbo Defrost</li><li>Keep Warm</li><li>Child Lock</li></ul>',
                'dp' => 10, 'guar' => 0, 'color' => '#6366f1',
                'variants' => [
                    ['name' => '20L', 'sku' => 'MW-PAN-20', 'price' => 350, 'stock' => 12, 'attrs' => ['Kapasiti' => '20L', 'Kuasa' => '800W']],
                    ['name' => '25L', 'sku' => 'MW-PAN-25', 'price' => 450, 'stock' => 8, 'attrs' => ['Kapasiti' => '25L', 'Kuasa' => '1000W']],
                    ['name' => '32L', 'sku' => 'MW-PAN-32', 'price' => 600, 'stock' => 5, 'attrs' => ['Kapasiti' => '32L', 'Kuasa' => '1200W']],
                ],
            ],
            [
                'name' => 'Vacuum Cleaner Philips',
                'desc' => '<p>Pembersih vakum Philips dengan penyedutan kuat dan penapis HEPA.</p><ul><li>PowerCyclone 5</li><li>HEPA 13 Filter</li><li>Bagless Design</li><li>5m Cable Length</li></ul>',
                'dp' => 10, 'guar' => 0, 'color' => '#4f46e5',
                'variants' => [
                    ['name' => 'Standard', 'sku' => 'VAC-PHIL-STD', 'price' => 500, 'stock' => 8, 'attrs' => ['Jenis' => 'Bagless', 'Kuasa' => '1500W']],
                    ['name' => 'Power Cyclonic', 'sku' => 'VAC-PHIL-PRO', 'price' => 750, 'stock' => 4, 'attrs' => ['Jenis' => 'Bagless', 'Kuasa' => '2000W']],
                ],
            ],
            [
                'name' => 'Rice Cooker Cosway',
                'desc' => '<p>Periuk nasi elektrik Cosway dengan teknologi 3D Heating.</p><ul><li>3D Heating System</li><li>Non-stick Inner Pot</li><li>Auto Keep Warm</li><li>Steam Function</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#4338ca',
                'variants' => [
                    ['name' => '1.5L', 'sku' => 'RC-COS-15', 'price' => 150, 'stock' => 15, 'attrs' => ['Kapasiti' => '1.5L', 'Sesuai' => '3-5 orang']],
                    ['name' => '3.0L', 'sku' => 'RC-COS-30', 'price' => 200, 'stock' => 10, 'attrs' => ['Kapasiti' => '3.0L', 'Sesuai' => '6-10 orang']],
                    ['name' => '5.0L', 'sku' => 'RC-COS-50', 'price' => 280, 'stock' => 6, 'attrs' => ['Kapasiti' => '5.0L', 'Sesuai' => '11-15 orang']],
                ],
            ],
            [
                'name' => 'Blender Philips',
                'desc' => '<p>Pengisar Philips dengan teknologi ProBlend untuk hasil yang licin.</p><ul><li>ProBlend 6 Technology</li><li>Stainless Steel Blade</li><li>Multi-speed Control</li><li>1.5L Capacity</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#3730a3',
                'variants' => [
                    ['name' => 'Standard', 'sku' => 'BLEND-PHIL-STD', 'price' => 180, 'stock' => 12, 'attrs' => ['Kapasiti' => '1.5L', 'Kelajuan' => '3 + Pulse']],
                    ['name' => 'Pro', 'sku' => 'BLEND-PHIL-PRO', 'price' => 300, 'stock' => 6, 'attrs' => ['Kapasiti' => '2.0L', 'Kelajuan' => '6 + Pulse']],
                ],
            ],
            [
                'name' => 'Water Heater Joven',
                'desc' => '<p>Pemanas air Joven dengan sistem keselamatan lengkap dan penjimatan tenaga.</p><ul><li>Inverter Technology</li><li>Safety Cut-off</li><li>Temperature Control</li><li>Anti-bacterial</li></ul>',
                'dp' => 10, 'guar' => 1, 'color' => '#312e81',
                'variants' => [
                    ['name' => '30L', 'sku' => 'WH-JOVEN-30', 'price' => 400, 'stock' => 8, 'attrs' => ['Kapasiti' => '30L', 'Jenis' => 'Simpanan']],
                    ['name' => '50L', 'sku' => 'WH-JOVEN-50', 'price' => 550, 'stock' => 5, 'attrs' => ['Kapasiti' => '50L', 'Jenis' => 'Simpanan']],
                ],
            ],
        ];

        foreach ($items as $i) {
            $this->createProduct($coopId, $catId, $storage, $i);
        }
    }

    private function seedFurniture(int $coopId, int $catId, $storage): void
    {
        $items = [
            [
                'name' => 'Set Sofa Moden',
                'desc' => '<p>Set sofa moden selesa untuk ruang tamu. Diperbuat daripada fabrik berkualiti tinggi.</p><ul><li>Bingkai kayu pejal</li><li>Fabrik kalis air</li><li>Kusyen busa densiti tinggi</li><li>Kaki krom</li></ul>',
                'dp' => 10, 'guar' => 1, 'color' => '#7c3aed',
                'variants' => [
                    ['name' => '3+1+1 (Kelabu)', 'sku' => 'SOFA-3P1P-GRY', 'price' => 1800, 'stock' => 4, 'attrs' => ['Warna' => 'Kelabu', 'Konfigurasi' => '3+1+1']],
                    ['name' => '3+2+1 (Biru)', 'sku' => 'SOFA-3P2P-BLU', 'price' => 2200, 'stock' => 3, 'attrs' => ['Warna' => 'Biru', 'Konfigurasi' => '3+2+1']],
                ],
            ],
            [
                'name' => 'Katil Queen Size',
                'desc' => '<p>Katil queen size dengan reka bentuk moden dan selesa.</p><ul><li>Bingkai kayu solid</li><li>Slat support system</li><li>Tilam spring pocket</li><li>5 tahun jaminan</li></ul>',
                'dp' => 10, 'guar' => 1, 'color' => '#6d28d9',
                'variants' => [
                    ['name' => 'Frame Only', 'sku' => 'BED-QUEEN-FRM', 'price' => 800, 'stock' => 6, 'attrs' => ['Saiz' => 'Queen (150x190cm)', 'Termasuk' => 'Frame sahaja']],
                    ['name' => 'Dengan Tilam', 'sku' => 'BED-QUEEN-FULL', 'price' => 1500, 'stock' => 4, 'attrs' => ['Saiz' => 'Queen (150x190cm)', 'Termasuk' => 'Frame + Tilam']],
                ],
            ],
            [
                'name' => 'Katil Single',
                'desc' => '<p>Katil single sesuai untuk bilik tidur anak-anak atau tetamu.</p><ul><li>Bingkai besi kukuh</li><li>Mudah dipasang</li><li>Tilam busa selesa</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#5b21b6',
                'variants' => [
                    ['name' => 'Frame Only', 'sku' => 'BED-SINGLE-FRM', 'price' => 400, 'stock' => 8, 'attrs' => ['Saiz' => 'Single (90x190cm)', 'Termasuk' => 'Frame sahaja']],
                    ['name' => 'Dengan Tilam', 'sku' => 'BED-SINGLE-FULL', 'price' => 800, 'stock' => 5, 'attrs' => ['Saiz' => 'Single (90x190cm)', 'Termasuk' => 'Frame + Tilam']],
                ],
            ],
            [
                'name' => 'Almari Pakaian 3 Pintu',
                'desc' => '<p>Almari pakaian 3 pintu dengan ruang penyimpanan yang luas.</p><ul><li>Material particle board</li><li>3 pintu sliding</li><li>Relung penyangkut</li><li>Laci dalaman</li></ul>',
                'dp' => 15, 'guar' => 1, 'color' => '#4c1d95',
                'variants' => [
                    ['name' => 'Oak', 'sku' => 'WARD-3DR-OAK', 'price' => 1200, 'stock' => 4, 'attrs' => ['Warna' => 'Oak', 'Bahan' => 'Particle Board']],
                    ['name' => 'White', 'sku' => 'WARD-3DR-WHT', 'price' => 1000, 'stock' => 5, 'attrs' => ['Warna' => 'Putih', 'Bahan' => 'Particle Board']],
                    ['name' => 'Walnut', 'sku' => 'WARD-3DR-WAL', 'price' => 1300, 'stock' => 3, 'attrs' => ['Warna' => 'Walnut', 'Bahan' => 'Particle Board']],
                ],
            ],
            [
                'name' => 'Almari Dapur',
                'desc' => '<p>Almari dapur dengan rak pelbagai fungsi untuk penyimpanan perkakas dapur.</p><ul><li>Bahan kalis air</li><li>Rak boleh laras</li><li>Pintu sliding</li><li>Bingkai aluminium</li></ul>',
                'dp' => 10, 'guar' => 0, 'color' => '#3b0764',
                'variants' => [
                    ['name' => '3 Kaki', 'sku' => 'CAB-KITCH-3', 'price' => 600, 'stock' => 6, 'attrs' => ['Saiz' => '3 Kaki', 'Tingkat' => '3']],
                    ['name' => '5 Kaki', 'sku' => 'CAB-KITCH-5', 'price' => 900, 'stock' => 4, 'attrs' => ['Saiz' => '5 Kaki', 'Tingkat' => '5']],
                ],
            ],
            [
                'name' => 'Meja Makan Set',
                'desc' => '<p>Set meja makan minimalis sesuai untuk keluarga moden.</p><ul><li>Permukaan kaca/wood</li><li>Kaki kayu kukuh</li><li>Mudah dibersihkan</li><li>Kerusi empuk</li></ul>',
                'dp' => 15, 'guar' => 1, 'color' => '#701a75',
                'variants' => [
                    ['name' => '4 Kerusi', 'sku' => 'DINE-4SET', 'price' => 1200, 'stock' => 5, 'attrs' => ['Saiz' => '4 Kerusi', 'Material' => 'Kayu + Kaca']],
                    ['name' => '6 Kerusi', 'sku' => 'DINE-6SET', 'price' => 1800, 'stock' => 3, 'attrs' => ['Saiz' => '6 Kerusi', 'Material' => 'Kayu + Kaca']],
                ],
            ],
            [
                'name' => 'Rak TV',
                'desc' => '<p>Rak TV minimalis dengan ruang storan multimedia.</p><ul><li>Material MDF</li><li>Rak terbuka & tertutup</li><li>Lubang cable management</li><li>Kapasiti beban tinggi</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#9d174d',
                'variants' => [
                    ['name' => 'Small', 'sku' => 'TVRACK-SML', 'price' => 250, 'stock' => 10, 'attrs' => ['Saiz' => '80cm', 'Tingkat' => '2']],
                    ['name' => 'Medium', 'sku' => 'TVRACK-MED', 'price' => 400, 'stock' => 6, 'attrs' => ['Saiz' => '120cm', 'Tingkat' => '3']],
                    ['name' => 'Large', 'sku' => 'TVRACK-LRG', 'price' => 600, 'stock' => 4, 'attrs' => ['Saiz' => '160cm', 'Tingkat' => '4']],
                ],
            ],
            [
                'name' => 'Meja Belajar',
                'desc' => '<p>Meja belajar ergonomik sesuai untuk pelajar dan pekerja pejabat.</p><ul><li>Permukaan luas</li><li>Ketinggian tetap</li><li>Laci storan</li><li>Bingkai besi kukuh</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#831843',
                'variants' => [
                    ['name' => 'Standard', 'sku' => 'DESK-STD', 'price' => 300, 'stock' => 8, 'attrs' => ['Saiz' => '90x50cm', 'Bahan' => 'MDF + Besi']],
                    ['name' => 'Dengan Rak Buku', 'sku' => 'DESK-BOOK', 'price' => 500, 'stock' => 5, 'attrs' => ['Saiz' => '90x50cm', 'Bahan' => 'MDF + Besi', 'Tambahan' => 'Rak Buku atas']],
                ],
            ],
            [
                'name' => 'Bookshelf',
                'desc' => '<p>Rak buku moden untuk penyimpanan buku dan hiasan.</p><ul><li>Material kayu</li><li>Rak boleh laras</li><li>Mudah dipasang</li><li>Pelbagai warna</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#be123c',
                'variants' => [
                    ['name' => '3 Tier', 'sku' => 'BOOK-3TIER', 'price' => 200, 'stock' => 10, 'attrs' => ['Tingkat' => '3', 'Tinggi' => '90cm']],
                    ['name' => '5 Tier', 'sku' => 'BOOK-5TIER', 'price' => 350, 'stock' => 6, 'attrs' => ['Tingkat' => '5', 'Tinggi' => '150cm']],
                ],
            ],
            [
                'name' => 'Set Sofa Kulit',
                'desc' => '<p>Set sofa kulit premium untuk ruang tamu mewah.</p><ul><li>Kulit PU asli</li><li>Bingkai kayu keras</li><li>Kusyen memory foam</li><li>Jaminan 5 tahun</li></ul>',
                'dp' => 20, 'guar' => 2, 'color' => '#881337',
                'variants' => [
                    ['name' => '3+1+1 (Coklat)', 'sku' => 'SOFA-LEATHER-BRN', 'price' => 2800, 'stock' => 2, 'attrs' => ['Warna' => 'Coklat', 'Bahan' => 'Kulit PU', 'Konfigurasi' => '3+1+1']],
                    ['name' => '3+2+1 (Hitam)', 'sku' => 'SOFA-LEATHER-BLK', 'price' => 3500, 'stock' => 2, 'attrs' => ['Warna' => 'Hitam', 'Bahan' => 'Kulit PU', 'Konfigurasi' => '3+2+1']],
                ],
            ],
        ];

        foreach ($items as $i) {
            $this->createProduct($coopId, $catId, $storage, $i);
        }
    }

    private function seedAppliances(int $coopId, int $catId, $storage): void
    {
        $items = [
            [
                'name' => 'Set Cookware Stainless',
                'desc' => '<p>Set perkakas masak stainless steel berkualiti tinggi.</p><ul><li>Stainless Steel 304</li><li>Kuali non-stick</li><li>Pemegang tahan panas</li><li>Serasi semua dapur</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#059669',
                'variants' => [
                    ['name' => '5 pcs', 'sku' => 'COOK-5PCS', 'price' => 250, 'stock' => 10, 'attrs' => ['Kuantiti' => '5 pcs', 'Bahan' => 'Stainless Steel']],
                    ['name' => '7 pcs', 'sku' => 'COOK-7PCS', 'price' => 380, 'stock' => 6, 'attrs' => ['Kuantiti' => '7 pcs', 'Bahan' => 'Stainless Steel']],
                    ['name' => '10 pcs', 'sku' => 'COOK-10PCS', 'price' => 550, 'stock' => 4, 'attrs' => ['Kuantiti' => '10 pcs', 'Bahan' => 'Stainless Steel']],
                ],
            ],
            [
                'name' => 'Periuk Nasi Elektrik',
                'desc' => '<p>Periuk nasi elektrik pelbagai fungsi dengan teknologi pemanasan sekata.</p><ul><li>Non-stick inner pot</li><li>Auto keep warm</li><li>Steam tray included</li><li>Measuring cup & spatula</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#047857',
                'variants' => [
                    ['name' => '1.5L', 'sku' => 'RICE-15', 'price' => 80, 'stock' => 20, 'attrs' => ['Kapasiti' => '1.5L', 'Sesuai' => '3-5 orang']],
                    ['name' => '3.0L', 'sku' => 'RICE-30', 'price' => 130, 'stock' => 15, 'attrs' => ['Kapasiti' => '3.0L', 'Sesuai' => '6-10 orang']],
                    ['name' => '5.0L', 'sku' => 'RICE-50', 'price' => 180, 'stock' => 10, 'attrs' => ['Kapasiti' => '5.0L', 'Sesuai' => '11-15 orang']],
                ],
            ],
            [
                'name' => 'Kettle Elektrik',
                'desc' => '<p>Pemanas air elektrik dengan elemen pemanas tersembunyi.</p><ul><li>Stainless steel body</li><li>Auto shut-off</li><li>Dry boil protection</li><li>360-degree base</li></ul>',
                'dp' => 0, 'guar' => 0, 'color' => '#065f46',
                'variants' => [
                    ['name' => '1.5L', 'sku' => 'KETTLE-15', 'price' => 60, 'stock' => 20, 'attrs' => ['Kapasiti' => '1.5L', 'Bahan' => 'Stainless Steel']],
                    ['name' => '1.7L', 'sku' => 'KETTLE-17', 'price' => 80, 'stock' => 15, 'attrs' => ['Kapasiti' => '1.7L', 'Bahan' => 'Stainless Steel']],
                    ['name' => '2.0L', 'sku' => 'KETTLE-20', 'price' => 100, 'stock' => 10, 'attrs' => ['Kapasiti' => '2.0L', 'Bahan' => 'Stainless Steel']],
                ],
            ],
            [
                'name' => 'Set Pinggan Makan',
                'desc' => '<p>Set pinggan makan seramik berkualiti tinggi dengan reka bentuk elegan.</p><ul><li>Seramik berkualiti</li><li>Microwave & dishwasher safe</li><li>Pelbagai saiz</li><li>Kotak hadiah</li></ul>',
                'dp' => 0, 'guar' => 0, 'color' => '#115e59',
                'variants' => [
                    ['name' => '12 pcs', 'sku' => 'PLATE-12PCS', 'price' => 90, 'stock' => 15, 'attrs' => ['Kuantiti' => '12 pcs', 'Bahan' => 'Seramik']],
                    ['name' => '24 pcs', 'sku' => 'PLATE-24PCS', 'price' => 160, 'stock' => 10, 'attrs' => ['Kuantiti' => '24 pcs', 'Bahan' => 'Seramik']],
                    ['name' => '36 pcs', 'sku' => 'PLATE-36PCS', 'price' => 220, 'stock' => 6, 'attrs' => ['Kuantiti' => '36 pcs', 'Bahan' => 'Seramik']],
                ],
            ],
            [
                'name' => 'Set Alat Makan',
                'desc' => '<p>Set alat makan stainless steel dengan reka bentuk moden.</p><ul><li>Stainless Steel 304</li><li>Ergonomic handle</li><li>Kemasan mirror polish</li><li>Antikarat</li></ul>',
                'dp' => 0, 'guar' => 0, 'color' => '#0f766e',
                'variants' => [
                    ['name' => '24 pcs', 'sku' => 'CUTLERY-24PCS', 'price' => 60, 'stock' => 20, 'attrs' => ['Kuantiti' => '24 pcs', 'Bahan' => 'Stainless Steel']],
                    ['name' => '36 pcs', 'sku' => 'CUTLERY-36PCS', 'price' => 90, 'stock' => 12, 'attrs' => ['Kuantiti' => '36 pcs', 'Bahan' => 'Stainless Steel']],
                ],
            ],
            [
                'name' => 'Iron Philips',
                'desc' => '<p>Seterika Philips dengan tapak seramik licin dan teknologi anti-karat.</p><ul><li>Ceramic Soleplate</li><li>Anti-drip system</li><li>Auto shut-off</li><li>Vertical steam</li></ul>',
                'dp' => 0, 'guar' => 0, 'color' => '#0d9488',
                'variants' => [
                    ['name' => 'Dry', 'sku' => 'IRON-PHIL-DRY', 'price' => 80, 'stock' => 12, 'attrs' => ['Jenis' => 'Dry', 'Kuasa' => '1200W']],
                    ['name' => 'Steam', 'sku' => 'IRON-PHIL-STEAM', 'price' => 130, 'stock' => 8, 'attrs' => ['Jenis' => 'Steam', 'Kuasa' => '1800W']],
                ],
            ],
            [
                'name' => 'Oven Kecil',
                'desc' => '<p>Ketuhar elektrik kecil sesuai untuk roti, pizza dan kek.</p><ul><li>Quartz heating element</li><li>Temperature control</li><li>Timer 60 minit</li><li>Baking tray included</li></ul>',
                'dp' => 5, 'guar' => 0, 'color' => '#14b8a6',
                'variants' => [
                    ['name' => '20L', 'sku' => 'OVEN-20L', 'price' => 180, 'stock' => 10, 'attrs' => ['Kapasiti' => '20L', 'Kuasa' => '1200W']],
                    ['name' => '30L', 'sku' => 'OVEN-30L', 'price' => 280, 'stock' => 6, 'attrs' => ['Kapasiti' => '30L', 'Kuasa' => '1500W']],
                    ['name' => '42L', 'sku' => 'OVEN-42L', 'price' => 400, 'stock' => 4, 'attrs' => ['Kapasiti' => '42L', 'Kuasa' => '1800W']],
                ],
            ],
            [
                'name' => 'Toaster',
                'desc' => '<p>Pembakar roti elektrik dengan fungsi defrost dan reheat.</p><ul><li>Auto centering</li><li>6 tahap kegaringan</li><li>Reheat & defrost</li><li>Crumb tray removable</li></ul>',
                'dp' => 0, 'guar' => 0, 'color' => '#2dd4bf',
                'variants' => [
                    ['name' => '2 Slice', 'sku' => 'TOAST-2SLICE', 'price' => 55, 'stock' => 15, 'attrs' => ['Kapasiti' => '2 keping', 'Fungsi' => 'Defrost, Reheat']],
                    ['name' => '4 Slice', 'sku' => 'TOAST-4SLICE', 'price' => 85, 'stock' => 10, 'attrs' => ['Kapasiti' => '4 keping', 'Fungsi' => 'Defrost, Reheat']],
                ],
            ],
            [
                'name' => 'Set Bekas Makanan',
                'desc' => '<p>Set bekas makanan plastik bertutup rapat pelbagai saiz.</p><ul><li>Plastik food-grade</li><li>Penutup kedap udara</li><li>Microwave & freezer safe</li><li>Boleh disusun</li></ul>',
                'dp' => 0, 'guar' => 0, 'color' => '#5eead4',
                'variants' => [
                    ['name' => '10 pcs', 'sku' => 'CONTAINER-10PCS', 'price' => 50, 'stock' => 20, 'attrs' => ['Kuantiti' => '10 pcs', 'Bahan' => 'Plastik Food-grade']],
                    ['name' => '18 pcs', 'sku' => 'CONTAINER-18PCS', 'price' => 80, 'stock' => 15, 'attrs' => ['Kuantiti' => '18 pcs', 'Bahan' => 'Plastik Food-grade']],
                    ['name' => '24 pcs', 'sku' => 'CONTAINER-24PCS', 'price' => 110, 'stock' => 10, 'attrs' => ['Kuantiti' => '24 pcs', 'Bahan' => 'Plastik Food-grade']],
                ],
            ],
            [
                'name' => 'Kipas Dinding',
                'desc' => '<p>Kipas dinding elektrik dengan putaran senyap dan angin kuat.</p><ul><li>Motor tembaga tulen</li><li>3 kelajuan</li><li>Pusingan senyap</li><li>Remote control included</li></ul>',
                'dp' => 0, 'guar' => 0, 'color' => '#99f6e4',
                'variants' => [
                    ['name' => '16 inci', 'sku' => 'FAN-WALL-16', 'price' => 80, 'stock' => 15, 'attrs' => ['Saiz' => '16"', 'Kelajuan' => '3']],
                    ['name' => '18 inci', 'sku' => 'FAN-WALL-18', 'price' => 100, 'stock' => 12, 'attrs' => ['Saiz' => '18"', 'Kelajuan' => '3']],
                    ['name' => '20 inci', 'sku' => 'FAN-WALL-20', 'price' => 130, 'stock' => 8, 'attrs' => ['Saiz' => '20"', 'Kelajuan' => '3']],
                ],
            ],
        ];

        foreach ($items as $i) {
            $this->createProduct($coopId, $catId, $storage, $i);
        }
    }

    private function createProduct(int $coopId, int $catId, $storage, array $item): void
    {
        $slug = str($item['name'])->slug();

        $product = AnsuranProduct::updateOrCreate(
            ['cooperative_id' => $coopId, 'slug' => $slug],
            [
                'ansuran_category_id' => $catId,
                'name' => $item['name'],
                'description' => $item['desc'],
                'min_down_payment_percent' => $item['dp'],
                'guarantor_count' => $item['guar'],
                'status' => 'aktif',
            ]
        );

        $imgPath = "ansuran/{$slug}.svg";
        if (! $storage->exists($imgPath)) {
            $storage->put($imgPath, $this->productSvg($item['name'], $item['color'] ?? '#6366f1'));
        }

        AnsuranProductImage::updateOrCreate(
            ['ansuran_product_id' => $product->id, 'is_primary' => true],
            ['path' => $imgPath, 'is_primary' => true]
        );

        foreach ($item['variants'] as $v) {
            AnsuranProductVariant::updateOrCreate(
                ['ansuran_product_id' => $product->id, 'sku' => $v['sku']],
                [
                    'name' => $v['name'],
                    'price' => $v['price'],
                    'stock' => $v['stock'],
                    'attributes' => $v['attrs'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function catImage($storage, string $slug, string $name, string $color): string
    {
        $path = "ansuran/cat-{$slug}.svg";
        if (! $storage->exists($path)) {
            $escaped = htmlspecialchars($name, ENT_XML1);
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="250" viewBox="0 0 400 250">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$color};stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#1e1b4b;stop-opacity:1"/>
    </linearGradient>
  </defs>
  <rect width="400" height="250" fill="url(#g)" rx="12"/>
  <text x="200" y="125" text-anchor="middle" dominant-baseline="middle" font-family="system-ui, sans-serif" font-size="36" font-weight="bold" fill="white">{$escaped}</text>
</svg>
SVG;
            $storage->put($path, $svg);
        }
        return $path;
    }

    private function productSvg(string $name, string $color): string
    {
        $escaped = htmlspecialchars($name, ENT_XML1);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$color};stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#1e1b4b;stop-opacity:1"/>
    </linearGradient>
  </defs>
  <rect width="600" height="600" fill="url(#g)" rx="16"/>
  <text x="300" y="280" text-anchor="middle" font-family="system-ui, sans-serif" font-size="32" font-weight="bold" fill="white">{$escaped}</text>
  <rect x="200" y="320" width="200" height="4" rx="2" fill="white" opacity="0.3"/>
  <text x="300" y="370" text-anchor="middle" font-family="system-ui, sans-serif" font-size="18" fill="white" opacity="0.7">Ansuran Mudah</text>
  <text x="300" y="400" text-anchor="middle" font-family="system-ui, sans-serif" font-size="16" fill="white" opacity="0.5">Koperasi Unikeb</text>
</svg>
SVG;
    }

    private function agreementTemplate(): string
    {
        return <<<'HTML'
<h2 style="text-align: center;">PERJANJIAN ANSURAN MUDAH</h2>

<p><strong>No Permohonan:</strong> {{no_ahli}}_ANSURAN</p>

<p>Perjanjian ini dibuat pada <strong>{{tarikh_kontrak}}</strong> antara:</p>

<p><strong>PIHAK PERTAMA:</strong> Koperasi Unikeb</p>
<p><strong>PIHAK KEDUA:</strong> {{nama_ahli}} (No KP: {{no_kad_pengenalan}})</p>

<hr>

<h3>BUTIRAN PRODUK</h3>
<table style="width: 100%; border-collapse: collapse;">
    <tr><td style="padding: 5px;"><strong>Produk:</strong></td><td style="padding: 5px;">{{nama_produk}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Varian:</strong></td><td style="padding: 5px;">{{varian}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Harga Penuh:</strong></td><td style="padding: 5px;">{{harga_penuh}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Bayaran Pendahuluan:</strong></td><td style="padding: 5px;">{{bayaran_pendahuluan}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Jumlah Pembiayaan:</strong></td><td style="padding: 5px;">{{jumlah_pembiayaan}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Kadar Keuntungan:</strong></td><td style="padding: 5px;">{{kadar_keuntungan}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Tempoh Ansuran:</strong></td><td style="padding: 5px;">{{tempoh_ansuran}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Bayaran Bulanan:</strong></td><td style="padding: 5px;">{{bayaran_bulanan}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Jumlah Perlu Dibayar:</strong></td><td style="padding: 5px;">{{jumlah_perlu_dibayar}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Kaedah Penerimaan:</strong></td><td style="padding: 5px;">{{kaedah_penghantaran}}</td></tr>
    <tr><td style="padding: 5px;"><strong>Alamat Penghantaran:</strong></td><td style="padding: 5px;">{{alamat_penghantaran}}</td></tr>
</table>

<hr>

<h3>SYARAT PERJANJIAN</h3>
<ol>
    <li>Pihak Kedua bersetuju membayar ansuran bulanan sebanyak {{bayaran_bulanan}} selama {{tempoh_ansuran}}.</li>
    <li>Bayaran hendaklah dibuat sebelum atau pada tarikh akhir setiap bulan.</li>
    <li>Kegagalan membayar ansuran selama 3 bulan berturut-turut akan menyebabkan tindakan undang-undang diambil.</li>
    <li>Produk akan menjadi hak milik Pihak Kedua selepas semua bayaran diselesaikan.</li>
</ol>
HTML;
    }
}
