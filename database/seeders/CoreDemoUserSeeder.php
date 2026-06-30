<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreDemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $cooperativeId = Cooperative::query()
            ->where('slug', 'koperasi-unikeb')
            ->value('id');

        if (! $cooperativeId) {
            $this->command->warn('Koperasi-unikeb not found. Skipping core demo users.');

            return;
        }

        $password = Hash::make('password');

        $superAdmin = User::query()->updateOrCreate([
            'email' => 'superadmin@iunikeb.com.my',
        ], [
            'name' => 'Super Admin Demo',
            'cooperative_id' => $cooperativeId,
            'role' => AccessControl::ROLE_SUPER_ADMIN,
            'user_type' => AccessControl::ROLE_SUPER_ADMIN,
            'status' => 'active',
            'password' => $password,
            'is_protected' => true,
        ]);
        $superAdmin->syncRoles([AccessControl::ROLE_SUPER_ADMIN]);

        $admin = User::query()->updateOrCreate([
            'email' => 'admin@iunikeb.com.my',
        ], [
            'name' => 'Pentadbir Demo',
            'cooperative_id' => $cooperativeId,
            'role' => User::ROLE_ADMIN,
            'user_type' => User::ROLE_ADMIN,
            'status' => 'active',
            'password' => $password,
            'is_protected' => true,
        ]);
        $admin->syncRoles([AccessControl::ROLE_ADMIN]);

        $member = User::query()->updateOrCreate([
            'email' => 'member@iunikeb.com.my',
        ], [
            'name' => 'Ahli Demo',
            'cooperative_id' => $cooperativeId,
            'role' => User::ROLE_MEMBER,
            'user_type' => User::ROLE_MEMBER,
            'status' => 'active',
            'password' => $password,
            'is_protected' => true,
        ]);
        $member->syncRoles([AccessControl::ROLE_MEMBER]);
    }
}
