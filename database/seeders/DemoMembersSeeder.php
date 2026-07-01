<?php

namespace Database\Seeders;

use App\Enums\MemberStatus;
use App\Models\Cooperative;
use App\Models\Member;
use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoMembersSeeder extends Seeder
{
    private array $firstNames = [
        'Ahmad', 'Mohd', 'Muhammad', 'Aiman', 'Amirul', 'Azman', 'Faisal', 'Hafiz',
        'Harith', 'Haziq', 'Ikhwan', 'Irfan', 'Iskandar', 'Kamal', 'Khairul',
        'Megat', 'Nasir', 'Nik', 'Rahim', 'Rashid', 'Saiful', 'Shahrul',
        'Syafiq', 'Syed', 'Wan', 'Zainal', 'Zulkifli', 'Azhar',
        'Aminah', 'Aisyah', 'Fatimah', 'Halimah', 'Khadijah', 'Mariam', 'Nurul',
        'Siti', 'Zaiton', 'Zarina', 'Azizah', 'Farah', 'Hafizah', 'Izzati',
        'Jamilah', 'Kartini', 'Mahani', 'Nadia', 'Noraini', 'Norsiah',
        'Sarah', 'Suzana', 'Zaleha', 'Zainab', 'Anis', 'Balqis', 'Dalilah',
    ];

    private array $lastNames = [
        'Abdullah', 'Abdul Rahman', 'Abdul Rahim', 'Abdul Hamid', 'Abdul Aziz',
        'Abdul Karim', 'Abdul Malik', 'Ibrahim', 'Ismail', 'Mohd Nor',
        'Mohd Ali', 'Mohd Yusof', 'Mohd Salleh', 'Osman', 'Rahman', 'Hashim',
        'Hussein', 'Yahaya', 'Yaakob', 'Mat', 'Hassan', 'Hussin', 'Sulaiman',
        'Harun', 'Idris', 'Alias', 'Bakar', 'Din', 'Jusoh', 'Kassim', 'Mahmud',
        'Musa', 'Othman', 'Ramli', 'Said', 'Talib', 'Yusoff', 'Zainuddin',
    ];

    public function run(): void
    {
        $cooperative = Cooperative::query()->where('slug', 'koperasi-unikeb')->first();
        if (! $cooperative) {
            return;
        }

        $admin = User::query()->where('email', 'admin@iunikeb.com.my')->first();
        $password = Hash::make('password');

        for ($i = 1; $i <= 30; $i++) {
            $email = 'member'.str_pad($i, 2, '0', STR_PAD_LEFT).'@iunikeb.com.my';

            if (User::query()->where('email', $email)->exists()) {
                continue;
            }

            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $name = $firstName.' '.$this->lastNames[array_rand($this->lastNames)];

            $user = User::query()->create([
                'cooperative_id' => $cooperative->id,
                'name' => $name,
                'email' => $email,
                'role' => User::ROLE_MEMBER,
                'user_type' => User::ROLE_MEMBER,
                'status' => 'active',
                'password' => $password,
                'email_verified_at' => now(),
            ]);
            $user->syncRoles([AccessControl::ROLE_MEMBER]);

            Member::factory()->create([
                'cooperative_id' => $cooperative->id,
                'user_id' => $user->id,
                'member_no' => 'DEMO-'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'full_name' => $name,
                'email' => $email,
                'approved_by' => $admin?->id,
                'membership_status' => MemberStatus::Active->value,
            ]);
        }
    }
}
