<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use App\Models\Poster;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PosterDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::query()->where('slug', 'koperasi-unikeb')->first();
        if (! $cooperative) {
            return;
        }

        $adminId = User::query()->where('email', 'admin@iunikeb.com.my')->value('id');
        $storage = Storage::disk('public');
        $storage->makeDirectory('posters');

        $items = [
            ['filename' => 'demo-poster-1.svg', 'bg' => '#d97706', 'label' => 'Infografik Caruman'],
            ['filename' => 'demo-poster-2.svg', 'bg' => '#dc2626', 'label' => 'Poster Program'],
            ['filename' => 'demo-poster-3.svg', 'bg' => '#2563eb', 'label' => 'Tip Kewangan'],
            ['filename' => 'demo-poster-4.svg', 'bg' => '#059669', 'label' => 'Promosi Ansuran'],
        ];

        foreach ($items as $i => $item) {
            $filePath = "posters/{$item['filename']}";

            if (! $storage->exists($filePath)) {
                $svg = $this->posterSvg($item['label'], $item['bg']);
                $storage->put($filePath, $svg);
            }

            Poster::query()->updateOrCreate(
                ['cooperative_id' => $cooperative->id, 'image_path' => $filePath],
                [
                    'link_url' => null,
                    'sort_order' => $i,
                    'is_active' => true,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }
    }

    private function posterSvg(string $label, string $bg): string
    {
        $escaped = htmlspecialchars($label, ENT_XML1);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1080" height="1350" viewBox="0 0 1080 1350">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bg};stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#7c3aed;stop-opacity:1"/>
    </linearGradient>
  </defs>
  <rect width="1080" height="1350" fill="url(#g)"/>
  <text x="540" y="675" dominant-baseline="middle" text-anchor="middle"
        font-family="system-ui, sans-serif" font-size="64" font-weight="bold" fill="white" opacity="0.9">{$escaped}</text>
</svg>
SVG;
    }
}
