<?php

namespace Database\Seeders;

use App\Enums\AttendanceMethod;
use App\Enums\ProgramStatus;
use App\Enums\RsvpResponse;
use App\Models\Cooperative;
use App\Models\Member;
use App\Models\Program;
use App\Models\ProgramRsvp;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProgramDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::query()->where('slug', 'koperasi-unikeb')->first();

        if (! $cooperative) {
            return;
        }

        $adminId = User::query()->where('email', 'admin@iunikeb.com.my')->value('id');
        $memberUserIds = Member::query()
            ->where('cooperative_id', $cooperative->id)
            ->where('membership_status', 'active')
            ->pluck('id')
            ->toArray();

        $storage = Storage::disk('public');
        $storage->makeDirectory('programs');

        $programsData = [
            [
                'title' => 'Mesyuarat Agung Tahunan 2026',
                'category' => 'agm',
                'program_type' => 'physical',
                'location' => 'Dewan Serbaguna Koperasi, Lot 1234, Jalan Koperasi',
                'capacity' => 300,
                'start_date' => now()->addMonths(2),
                'end_date' => now()->addMonths(2)->addHours(5),
                'registration_deadline' => now()->addMonths(2)->subWeek(),
                'description' => "Mesyuarat Agung Tahunan (MAT) Koperasi Unikeb bagi tahun kewangan 2025.\n\nAntara agenda:\n- Pembentangan Laporan Tahunan\n- Pengesahan Penyata Kewangan\n- Pelantikan Juruaudit\n- Pembahagian Dividen\n- Pemilihan Ahli Lembaga",
                'status' => ProgramStatus::Published->value,
                'is_featured' => true,
                'image_color' => '#1d4ed8',
            ],
            [
                'title' => 'Seminar Kewangan Ahli 2026',
                'category' => 'seminar',
                'program_type' => 'hybrid',
                'location' => 'Hotel Grand Riverview, Kajang',
                'online_url' => 'https://zoom.us/j/koperasihub-seminar',
                'capacity' => 150,
                'start_date' => now()->addMonth(),
                'end_date' => now()->addMonth()->addDays(1),
                'registration_deadline' => now()->addMonth()->subWeek(),
                'description' => "Seminar kewangan untuk ahli koperasi. Topik merangkumi pengurusan kewangan peribadi, pelaburan koperasi, dan perancangan persaraan.\n\nYuran: Percuma untuk ahli koperasi.\n\nTermasuk: Makan tengahari, kit seminar, dan sijil penyertaan.",
                'status' => ProgramStatus::Published->value,
                'is_featured' => true,
                'image_color' => '#7c3aed',
            ],
            [
                'title' => 'Kursus Asas Keusahawanan Digital',
                'category' => 'kursus',
                'program_type' => 'online',
                'online_url' => 'https://meet.google.com/koperasi-2026',
                'capacity' => 50,
                'start_date' => now()->addWeeks(3),
                'end_date' => now()->addWeeks(3)->addHours(3),
                'registration_deadline' => now()->addWeeks(2),
                'description' => "Kursus percuma untuk ahli yang berminat menceburi bidang perniagaan digital. Peserta akan belajar asas pemasaran media sosial, pembangunan laman web mudah, dan pengurusan kedai online.",
                'status' => ProgramStatus::Published->value,
                'is_featured' => false,
                'image_color' => '#059669',
            ],
            [
                'title' => 'Hari Keluarga Koperasi Unikeb',
                'category' => 'community',
                'program_type' => 'physical',
                'location' => 'Taman Botani Shah Alam',
                'capacity' => 500,
                'start_date' => now()->addMonths(3),
                'end_date' => now()->addMonths(3)->addDays(1),
                'registration_deadline' => now()->addMonths(3)->subWeeks(2),
                'description' => "Hari keluarga tahunan untuk semua ahli koperasi dan keluarga.\n\nAktiviti:\n- Sukaneka keluarga\n- Cabutan bertuah\n- Jualan murah\n- Aktiviti kanak-kanak\n- Makan bersama",
                'status' => ProgramStatus::Published->value,
                'is_featured' => true,
                'image_color' => '#d97706',
            ],
            [
                'title' => 'Webinar: Pelaburan Bijak untuk Ahli Koperasi',
                'category' => 'webinar',
                'program_type' => 'online',
                'online_url' => 'https://zoom.us/j/webinar-pelaburan',
                'capacity' => 200,
                'start_date' => now()->addWeeks(1)->addDays(2),
                'end_date' => now()->addWeeks(1)->addDays(2)->addHours(2),
                'registration_deadline' => now()->addWeeks(1)->addDay(),
                'description' => "Webinar santai tentang asas pelaburan yang sesuai untuk ahli koperasi. Dibimbing oleh pensijil kewangan bertauliah. Topik termasuk pelaburan dalam koperasi, ASNB, dan emas.",
                'status' => ProgramStatus::Published->value,
                'is_featured' => false,
                'image_color' => '#0d9488',
            ],
            [
                'title' => 'Program Gotong Royong Komuniti',
                'category' => 'volunteer',
                'program_type' => 'physical',
                'location' => 'Kawasan Perumahan Ahli Koperasi, Taman Sejahtera',
                'capacity' => 80,
                'start_date' => now()->addWeeks(4),
                'end_date' => now()->addWeeks(4)->addHours(6),
                'registration_deadline' => now()->addWeeks(3)->addDays(5),
                'description' => "Program sukarelawan membersihkan dan menceriakan kawasan perumahan ahli koperasi. Semua ahli dialu-alukan.\n\nSumbangan: Sediakan peralatan kebersihan asas.\nMakan tengahari dan minuman disediakan.",
                'status' => ProgramStatus::Published->value,
                'is_featured' => false,
                'image_color' => '#dc2626',
            ],
            [
                'title' => 'Seminar Perancangan Persaraan',
                'category' => 'seminar',
                'program_type' => 'physical',
                'location' => 'Pejabat Koperasi, Aras 3, Wisma Koperasi',
                'capacity' => 40,
                'start_date' => now()->addMonths(1)->addWeeks(2),
                'end_date' => now()->addMonths(1)->addWeeks(2)->addHours(4),
                'registration_deadline' => now()->addMonths(1)->addWeek(),
                'description' => 'Seminar eksklusif untuk ahli yang bakal bersara. Ketahui cara merancang kewangan persaraan dengan bijak menggunakan kemudahan dan produk koperasi.',
                'status' => ProgramStatus::Draft->value,
                'is_featured' => false,
                'image_color' => '#7c3aed',
            ],
            [
                'title' => 'Mesyuarat Agung Tahunan 2025',
                'category' => 'agm',
                'program_type' => 'physical',
                'location' => 'Dewan Serbaguna Koperasi',
                'capacity' => 280,
                'start_date' => now()->subMonths(6),
                'end_date' => now()->subMonths(6)->addHours(5),
                'description' => "Mesyuarat Agung Tahunan (MAT) Koperasi Unikeb bagi tahun kewangan 2024. (Acara lepas)",
                'status' => ProgramStatus::Completed->value,
                'is_featured' => false,
                'image_color' => '#6b7280',
            ],
            [
                'title' => 'Seminar Kewangan 2025',
                'category' => 'seminar',
                'program_type' => 'physical',
                'location' => 'Hotel Grand Riverview',
                'capacity' => 120,
                'start_date' => now()->subMonths(4),
                'end_date' => now()->subMonths(4)->addDays(1),
                'description' => 'Seminar kewangan tahun lepas. (Acara lepas)',
                'status' => ProgramStatus::Completed->value,
                'is_featured' => false,
                'image_color' => '#9ca3af',
            ],
        ];

        $programIds = [];
        foreach ($programsData as $data) {
            $slug = str($data['title'])->slug();

            $imageColor = $data['image_color'] ?? '#2563eb';
            $imagePath = "programs/{$slug}.svg";

            if (! $storage->exists($imagePath)) {
                $storage->put($imagePath, $this->programSvg($data['title'], $data['category'] ?? 'event', $imageColor));
            }

            $program = Program::query()->updateOrCreate(
                ['cooperative_id' => $cooperative->id, 'title' => $data['title']],
                [
                    'slug' => $slug,
                    'cooperative_id' => $cooperative->id,
                    'description' => $data['description'] ?? null,
                    'category' => $data['category'] ?? null,
                    'program_type' => $data['program_type'] ?? 'physical',
                    'location' => $data['location'] ?? null,
                    'online_url' => $data['online_url'] ?? null,
                    'capacity' => $data['capacity'] ?? null,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'] ?? null,
                    'registration_deadline' => $data['registration_deadline'] ?? null,
                    'cover_image_path' => $imagePath,
                    'status' => $data['status'] ?? ProgramStatus::Published->value,
                    'is_featured' => $data['is_featured'] ?? false,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ],
            );
            $programIds[] = $program->id;
        }

        if ($memberUserIds === []) {
            return;
        }

        $responses = [RsvpResponse::Hadir->value, RsvpResponse::Hadir->value, RsvpResponse::Hadir->value, RsvpResponse::Mungkin->value, RsvpResponse::TidakHadir->value];

        foreach ($programIds as $programId) {
            $assigned = [];
            $numRsvps = min(count($memberUserIds), rand(4, 8));

            $shuffled = $memberUserIds;
            shuffle($shuffled);
            $selectedMembers = array_slice($shuffled, 0, $numRsvps);

            foreach ($selectedMembers as $memberId) {
                $response = $responses[array_rand($responses)];
                $rsvp = ProgramRsvp::query()->updateOrCreate(
                    ['program_id' => $programId, 'member_id' => $memberId],
                    [
                        'cooperative_id' => $cooperative->id,
                        'response' => $response,
                        'responded_at' => now()->subDays(rand(0, 14)),
                    ],
                );

                if ($response === RsvpResponse::Hadir->value && rand(0, 1) === 1) {
                    $rsvp->update([
                        'checked_in_at' => $rsvp->responded_at?->copy()->addHours(rand(1, 5)),
                        'checked_in_by' => $adminId,
                        'attendance_method' => AttendanceMethod::ManualEntry->value,
                    ]);
                }

                $assigned[] = $memberId;
            }
        }
    }

    private function programSvg(string $title, string $category, string $bg): string
    {
        $escaped = htmlspecialchars($title, ENT_XML1);
        $icons = [
            'agm' => "\xF0\x9F\x93\x8A",
            'seminar' => "\xF0\x9F\x8E\xA4",
            'kursus' => "\xF0\x9F\x92\xBB",
            'community' => "\xF0\x9F\x91\xA8\xE2\x80\x8D\xF0\x9F\x91\xA9\xE2\x80\x8D\xF0\x9F\x91\xA7",
            'webinar' => "\xF0\x9F\x96\xA5\xEF\xB8\x8F",
            'volunteer' => "\xF0\x9F\xA4\x9D",
            'event' => "\xF0\x9F\x93\x85",
        ];
        $icon = $icons[$category] ?? "\xF0\x9F\x93\x85";

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bg};stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#1e1b4b;stop-opacity:1"/>
    </linearGradient>
    <pattern id="dots" patternUnits="userSpaceOnUse" width="40" height="40">
      <circle cx="2" cy="2" r="1.5" fill="white" opacity="0.05"/>
    </pattern>
  </defs>
  <rect width="800" height="450" fill="url(#g)"/>
  <rect width="800" height="450" fill="url(#dots)"/>
  <circle cx="650" cy="80" r="120" fill="white" opacity="0.03"/>
  <circle cx="100" cy="400" r="80" fill="white" opacity="0.03"/>
  <text x="400" y="140" text-anchor="middle" font-size="64">{$icon}</text>
  <text x="400" y="250" text-anchor="middle" font-family="system-ui, sans-serif" font-size="32" font-weight="bold" fill="white">{$escaped}</text>
  <rect x="320" y="280" width="160" height="4" rx="2" fill="white" opacity="0.3"/>
  <text x="400" y="330" text-anchor="middle" font-family="system-ui, sans-serif" font-size="18" fill="white" opacity="0.7">Koperasi Unikeb</text>
</svg>
SVG;
    }
}
