<?php

namespace Database\Seeders;

use App\Enums\MembershipApplicationStatus;
use App\Models\Cooperative;
use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipApplicationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::query()->where('slug', 'koperasi-unikeb')->first();
        $reviewerId = User::query()->where('email', 'admin@iunikeb.com.my')->value('id');

        if (! $cooperative) {
            return;
        }

        $applications = [
            [
                'application_no' => 'APP-'.now()->format('Ymd').'-0001',
                'full_name' => 'Nur Aina Binti Hassan',
                'identity_no' => '900101105432',
                'email' => 'aina.hassan@example.test',
                'phone' => '0123456789',
                'status' => MembershipApplicationStatus::Pending->value,
                'submitted_at' => now()->subDays(2),
                'reviewed_by' => null,
                'review_notes' => null,
                'reviewed_at' => null,
                'metadata' => [
                    'membership_type' => 'Individu',
                    'notes' => 'Berminat untuk menyertai program simpanan dan kebajikan ahli.',
                ],
            ],
            [
                'application_no' => 'APP-'.now()->format('Ymd').'-0002',
                'full_name' => 'Muhammad Firdaus Bin Rahman',
                'identity_no' => '880212106543',
                'email' => 'firdaus.rahman@example.test',
                'phone' => '0134567890',
                'status' => MembershipApplicationStatus::UnderReview->value,
                'submitted_at' => now()->subDay(),
                'reviewed_by' => $reviewerId,
                'review_notes' => 'Semakan dokumen sedang dibuat.',
                'reviewed_at' => now()->subHours(6),
                'metadata' => [
                    'membership_type' => 'Individu',
                ],
            ],
            [
                'application_no' => 'APP-'.now()->format('Ymd').'-0003',
                'full_name' => 'Ahli Demo',
                'identity_no' => '910101105555',
                'email' => 'member@iunikeb.com.my',
                'phone' => '0121111111',
                'status' => MembershipApplicationStatus::Approved->value,
                'submitted_at' => now()->subMonths(8)->subDays(5),
                'reviewed_by' => $reviewerId,
                'review_notes' => 'Permohonan diluluskan selepas semakan dokumen lengkap.',
                'reviewed_at' => now()->subMonths(8),
                'metadata' => [
                    'membership_type' => 'Individu',
                ],
            ],
        ];

        foreach ($applications as $data) {
            MembershipApplication::query()->firstOrCreate(
                [
                    'cooperative_id' => $cooperative->id,
                    'application_no' => $data['application_no'],
                ],
                [
                    'full_name' => $data['full_name'],
                    'identity_no' => $data['identity_no'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'status' => $data['status'],
                    'submitted_at' => $data['submitted_at'],
                    'reviewed_by' => $data['reviewed_by'],
                    'review_notes' => $data['review_notes'],
                    'reviewed_at' => $data['reviewed_at'],
                    'metadata' => $data['metadata'],
                ]
            );
        }
    }
}
