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
        $storage->makeDirectory('demo');

        $items = [
            [
                'title' => 'Promosi Pembiayaan Peribadi',
                'alt_text' => 'Banner promosi pembiayaan peribadi',
                'filename' => 'banner-pembiayaan.svg',
                'bg' => '#0f766e',
                'type' => 'banner',
                'audience' => 'members',
                'sort_order' => 1,
            ],
            [
                'title' => 'Program Khidmat Komuniti',
                'alt_text' => 'Banner program khidmat komuniti',
                'filename' => 'banner-program.svg',
                'bg' => '#1d4ed8',
                'type' => 'banner',
                'audience' => 'members',
                'sort_order' => 2,
            ],
            [
                'title' => 'Selamat Hari Raya',
                'alt_text' => 'Ucapan Selamat Hari Raya',
                'filename' => 'banner-raya.svg',
                'bg' => '#7c3aed',
                'type' => 'banner',
                'audience' => 'both',
                'sort_order' => 3,
            ],
            [
                'title' => 'Infografik Caruman',
                'alt_text' => 'Poster infografik caruman ahli',
                'filename' => 'poster-caruman.svg',
                'bg' => '#d97706',
                'type' => 'poster',
                'audience' => 'members',
                'sort_order' => 1,
            ],
            [
                'title' => 'Poster Program',
                'alt_text' => 'Poster program akan datang',
                'filename' => 'poster-program.svg',
                'bg' => '#dc2626',
                'type' => 'poster',
                'audience' => 'both',
                'sort_order' => 2,
            ],
        ];

        $fontsDir = resource_path('fonts');
        $fontPath = null;
        if (file_exists("{$fontsDir}/NotoSans-Bold.ttf")) {
            $fontPath = "{$fontsDir}/NotoSans-Bold.ttf";
        } elseif (file_exists("{$fontsDir}/DejaVuSans-Bold.ttf")) {
            $fontPath = "{$fontsDir}/DejaVuSans-Bold.ttf";
        }

        foreach ($items as $i => $item) {
            $filePath = "demo/{$item['filename']}";

            if (! $storage->exists($filePath)) {
                $title = $item['title'];
                $styled = $fontPath
                    ? $this->svgWithEmbeddedFont($title, $item['bg'])
                    : $this->simpleSvg($title, $item['bg']);
                $storage->put($filePath, $styled);
            }

            Poster::query()->create([
                'cooperative_id' => $cooperative->id,
                'title' => $item['title'],
                'alt_text' => $item['alt_text'],
                'image_path' => $filePath,
                'link_url' => match ($i) {
                    0 => route('member.financing.index'),
                    1 => route('member.programs.index'),
                    default => null,
                },
                'type' => $item['type'],
                'audience' => $item['audience'],
                'is_active' => true,
                'sort_order' => $item['sort_order'],
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
        }
    }

    private function simpleSvg(string $title, string $bg): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="400" viewBox="0 0 1200 400">
  <rect width="1200" height="400" fill="{$bg}"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
        font-family="system-ui, sans-serif" font-size="32" font-weight="bold" fill="white">{$title}</text>
</svg>
SVG;
    }

    private function svgWithEmbeddedFont(string $title, string $bg): string
    {
        return $this->simpleSvg($title, $bg);
    }
}
