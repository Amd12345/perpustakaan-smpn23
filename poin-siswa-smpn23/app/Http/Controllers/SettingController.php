<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Setting;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'schoolName' => Setting::getValue('school_name', ''),
            'schoolAddress' => Setting::getValue('school_address', ''),
            'schoolCity' => Setting::getValue('school_city', ''),
            'schoolPhone' => Setting::getValue('school_phone', ''),
            'schoolEmail' => Setting::getValue('school_email', ''),
            'waThreshold' => Setting::getValue('wa_threshold', 25),
            'waAchievementEnabled' => (bool) Setting::getValue('wa_achievement_enabled', true),
            'conversionRatio' => Setting::getValue('point_conversion_ratio', 1),
            'fonnteKeyConfigured' => (bool) env('FONTE_API_KEY'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_address' => ['nullable', 'string'],
            'school_city' => ['nullable', 'string', 'max:100'],
            'school_phone' => ['nullable', 'string', 'max:20'],
            'school_email' => ['nullable', 'email', 'max:100'],
            'wa_threshold' => ['required', 'integer', 'min:1', 'max:10000'],
            'wa_achievement_enabled' => ['nullable', 'boolean'],
            'point_conversion_ratio' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        foreach ([
            'school_name',
            'school_address',
            'school_city',
            'school_phone',
            'school_email',
            'wa_threshold',
            'point_conversion_ratio',
        ] as $key) {
            Setting::setValue($key, $data[$key], str_starts_with($key, 'wa_') ? 'whatsapp' : (str_starts_with($key, 'school_') ? 'sekolah' : 'poin'));
        }

        Setting::setValue('wa_achievement_enabled', $request->boolean('wa_achievement_enabled') ? '1' : '0', 'whatsapp');

        ActivityLogService::log('update_settings', null, 'Memperbarui pengaturan sistem');

        return back()->with('success', 'Pengaturan disimpan.');
    }
}
