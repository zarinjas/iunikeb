<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use App\Models\Unit;
use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(CooperativeSettingsSeeder::class);
        $this->call(CoreDemoUserSeeder::class);

        User::query()
            ->whereIn('role', AccessControl::roles())
            ->each(fn (User $user) => $user->syncRoles([$user->role]));

        $this->call(FrontpageDemoSeeder::class);
        $this->call(MemberDemoSeeder::class);
        $this->call(DemoMembersSeeder::class);
        $this->call(MembershipApplicationDemoSeeder::class);
        $this->call(ComplaintDemoSeeder::class);
        $this->call(CarumanDemoSeeder::class);
        $this->call(UnitDemoSeeder::class);
        $this->call(FinancingDemoSeeder::class);
        $this->call(ProgramDemoSeeder::class);
        $this->call(AnsuranMudahDemoSeeder::class);
        $this->call(BannerDemoSeeder::class);
        $this->call(PosterDemoSeeder::class);

        $cooperativeId = Cooperative::query()
            ->where('slug', 'koperasi-unikeb')
            ->value('id');

        $admin = User::query()
            ->where('email', 'admin@iunikeb.com.my')
            ->first();

        if ($cooperativeId && $admin) {
            $unitKeanggotaan = Unit::query()
                ->where('cooperative_id', $cooperativeId)
                ->where('slug', 'unit-keanggotaan')
                ->first();

            if ($unitKeanggotaan) {
                $admin->update([
                    'unit_id' => $unitKeanggotaan->id,
                    'staff_id' => 'STF001',
                    'position_title' => 'Pegawai Keanggotaan',
                ]);
            }
        }
    }
}
