<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use App\Services\Settings\SettingsService;
use Illuminate\Database\Seeder;

class CooperativeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $cooperative = Cooperative::query()->updateOrCreate([
            'slug' => 'koperasi-unikeb',
        ], [
            'name' => 'Koperasi Unikeb',
            'short_name' => 'Unikeb',
            'registration_no' => 'D-0-0000',
            'primary_color' => '#0F766E',
            'secondary_color' => '#1D4ED8',
            'address_line_1' => 'Aras 1, Bangunan Demo',
            'address_line_2' => 'Jalan Contoh 1',
            'city' => 'Kuala Lumpur',
            'state' => 'Wilayah Persekutuan Kuala Lumpur',
            'postcode' => '50450',
            'country' => 'Malaysia',
            'phone' => '+603-1234 5678',
            'email' => 'hello@iunikeb.com.my',
            'whatsapp' => '+6012-345 6789',
            'website_url' => 'https://iunikeb.com.my',
            'facebook_url' => null,
            'instagram_url' => null,
            'linkedin_url' => null,
            'footer_text' => 'Platform demo untuk pengurusan koperasi.',
            'status' => 'active',
        ]);

        app(SettingsService::class)->update($cooperative, [
            'brand' => [
                'name' => 'Koperasi Unikeb',
                'short_name' => 'Unikeb',
                'registration_no' => 'D-0-0000',
                'primary_color' => '#0F766E',
                'secondary_color' => '#1D4ED8',
            ],
            'contact' => [
                'address_line_1' => 'Aras 1, Bangunan Demo',
                'address_line_2' => 'Jalan Contoh 1',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan Kuala Lumpur',
                'postcode' => '50450',
                'country' => 'Malaysia',
                'phone' => '+603-1234 5678',
                'email' => 'hello@iunikeb.com.my',
                'whatsapp' => '+6012-345 6789',
                'website_url' => 'https://iunikeb.com.my',
            ],
            'social' => [
                'facebook_url' => null,
                'instagram_url' => null,
                'linkedin_url' => null,
            ],
            'seo' => [
                'meta_title' => 'Koperasi Unikeb',
                'meta_description' => 'Laman demo untuk platform pengurusan koperasi putih label.',
            ],
            'system' => [
                'timezone' => 'Asia/Kuala_Lumpur',
                'date_format' => 'd/m/Y',
            ],
            'referral' => [
                'commission_amount' => '20.00',
                'commission_enabled' => '1',
                'minimum_active_days' => '0',
            ],
        ]);
    }
}
