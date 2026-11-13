<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AchievementCategory;
use App\Models\PointRule;
use App\Models\ViolationCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        if (! $academicYear) {
            return;
        }

        $violations = [
            ['name' => 'Terlambat masuk', 'point' => 5, 'description' => 'Datang setelah bel masuk berbunyi'],
            ['name' => 'Tidak mengerjakan tugas', 'point' => 10, 'description' => 'Tidak menyerahkan tugas sesuai batas waktu'],
            ['name' => 'Membolos', 'point' => 20, 'description' => 'Tidak hadir tanpa keterangan yang sah'],
            ['name' => 'Berkelahi', 'point' => 50, 'description' => 'Terlibat perkelahian di dalam atau luar sekolah'],
        ];

        foreach ($violations as $item) {
            $category = ViolationCategory::firstOrCreate(
                ['name' => $item['name']],
                ['description' => $item['description'], 'is_active' => true],
            );

            PointRule::firstOrCreate(
                [
                    'ruleable_type' => ViolationCategory::class,
                    'ruleable_id' => $category->id,
                    'academic_year_id' => $academicYear->id,
                ],
                ['point_value' => $item['point'], 'is_active' => true],
            );
        }

        $achievements = [
            ['name' => 'Juara kelas', 'level' => 'kelas', 'point' => 10],
            ['name' => 'Juara tingkat sekolah', 'level' => 'sekolah', 'point' => 10],
            ['name' => 'Juara tingkat kota', 'level' => 'kota', 'point' => 25],
            ['name' => 'Juara tingkat provinsi', 'level' => 'provinsi', 'point' => 50],
            ['name' => 'Juara tingkat nasional', 'level' => 'nasional', 'point' => 100],
        ];

        foreach ($achievements as $item) {
            $category = AchievementCategory::firstOrCreate(
                ['name' => $item['name']],
                ['level' => $item['level'], 'description' => null, 'is_active' => true],
            );

            PointRule::firstOrCreate(
                [
                    'ruleable_type' => AchievementCategory::class,
                    'ruleable_id' => $category->id,
                    'academic_year_id' => $academicYear->id,
                ],
                ['point_value' => $item['point'], 'is_active' => true],
            );
        }
    }
}
