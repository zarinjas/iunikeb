<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Cooperative;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BannerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::query()->where('slug', 'koperasi-unikeb')->first();
        if (! $cooperative) {
            return;
        }

        $adminId = User::query()->where('email', 'admin@iunikeb.com.my')->value('id');
        $storage = Storage::disk('public');
        $storage->makeDirectory('banners');

        $items = [
            ['filename' => 'demo-banner-1.svg', 'bg' => '#0f766e', 'label' => 'Promosi Pembiayaan Peribadi'],
            ['filename' => 'demo-banner-2.svg', 'bg' => '#1d4ed8', 'label' => 'Program Khidmat Komuniti'],
            ['filename' => 'demo-banner-3.svg', 'bg' => '#7c3aed', 'label' => 'Selamat Hari Raya'],
        ];

        foreach ($items as $i => $item) {
            $filePath = "banners/{$item['filename']}";

            if (! $storage->exists($filePath)) {
                $svg = $this->bannerSvg($item['label'], $item['bg']);
                $storage->put($filePath, $svg);
            }

            Banner::query()->create([
                'cooperative_id' => $cooperative->id,
                'image_path' => $filePath,
                'link_url' => $i === 0 ? route('member.financing.index') : null,
                'sort_order' => $i,
                'is_active' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
        }
    }

    private function bannerSvg(string $label, string $bg): string
    {
        $escaped = htmlspecialchars($label, ENT_XML1);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="400" viewBox="0 0 1200 400">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bg};stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#0d9488;stop-opacity:1"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="400" fill="url(#g)"/>
  <text x="600" y="200" dominant-baseline="middle" text-anchor="middle"
        font-family="system-ui, sans-serif" font-size="40" font-weight="bold" fill="white" opacity="0.9">{$escaped}</text>
</svg>
SVG;
    }
}
