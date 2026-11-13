<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            ['start_year' => 2026, 'end_year' => 2027, 'is_active' => true],
        );

        if (AcademicYear::count() === 1) {
            $year->update(['is_active' => true]);
        }

        Setting::setValue('active_academic_year_id', (string) $year->id, 'akademik');
    }
}
