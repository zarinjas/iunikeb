<?php

namespace App\Console\Commands;

use App\Models\Cooperative;
use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedDemoUsers extends Command
{
    protected $signature = 'demo:seed-users';

    protected $description = 'Create demo users for quick login';

    public function handle(): void
    {
        $cooperativeId = Cooperative::query()
            ->where('slug', 'koperasi-unikeb')
            ->value('id');

        if (! $cooperativeId) {
            $this->warn('Cooperative "koperasi-unikeb" tidak dijumpai.');
            return;
        }

        $password = Hash::make('password');

        $users = [
            [
                'email' => 'superadmin@iunikeb.com.my',
                'name' => 'Super Admin Demo',
                'role' => AccessControl::ROLE_SUPER_ADMIN,
                'user_type' => AccessControl::ROLE_SUPER_ADMIN,
            ],
            [
                'email' => 'admin@iunikeb.com.my',
                'name' => 'Pentadbir Demo',
                'role' => User::ROLE_ADMIN,
                'user_type' => User::ROLE_ADMIN,
            ],
            [
                'email' => 'member@iunikeb.com.my',
                'name' => 'Ahli Demo',
                'role' => User::ROLE_MEMBER,
                'user_type' => User::ROLE_MEMBER,
            ],
        ];

        foreach ($users as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'cooperative_id' => $cooperativeId,
                    'role' => $data['role'],
                    'user_type' => $data['user_type'],
                    'status' => 'active',
                    'password' => $password,
                ]
            );
            $user->syncRoles([$data['role']]);
            $this->info("✓ {$data['name']} ({$data['email']})");
        }

        $this->newLine();
        $this->info('Demo users siap. Password: password');
    }
}
