<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'school_name', 'value' => 'SMP Negeri 23 Kota Tangerang Selatan', 'group' => 'sekolah'],
            ['key' => 'school_address', 'value' => 'Jl. Pendidikan No. 23, Kota Tangerang Selatan', 'group' => 'sekolah'],
            ['key' => 'school_city', 'value' => 'Kota Tangerang Selatan', 'group' => 'sekolah'],
            ['key' => 'school_phone', 'value' => '', 'group' => 'sekolah'],
            ['key' => 'school_email', 'value' => '', 'group' => 'sekolah'],
            ['key' => 'wa_threshold', 'value' => '25', 'group' => 'whatsapp', 'description' => 'Ambang global poin pelanggaran untuk notifikasi WhatsApp'],
            ['key' => 'wa_achievement_enabled', 'value' => '1', 'group' => 'whatsapp', 'description' => 'Kirim notifikasi WhatsApp setiap prestasi dicatat'],
            ['key' => 'notification_achievement_enabled', 'value' => '1', 'group' => 'whatsapp'],
            ['key' => 'point_conversion_ratio', 'value' => '1', 'group' => 'poin', 'description' => 'Rasio konversi poin prestasi ke pengurangan poin pelanggaran'],
        ];

        foreach ($settings as $setting) {
            Setting::setValue($setting['key'], $setting['value'], $setting['group'] ?? 'umum');

            if (! empty($setting['description'])) {
                Setting::where('key', $setting['key'])->update(['description' => $setting['description']]);
            }
        }
    }
}
