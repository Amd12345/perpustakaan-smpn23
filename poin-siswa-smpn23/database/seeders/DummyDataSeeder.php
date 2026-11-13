<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\PointRule;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Services\PointService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    private array $maleFirst = [
        'Ahmad', 'Budi', 'Rizky', 'Dimas', 'Fajar', 'Andi', 'Yoga', 'Bayu', 'Dedi',
        'Ilham', 'Farhan', 'Hendra', 'Joko', 'Kevin', 'Lukman', 'Nanda', 'Oscar',
        'Putra', 'Reza', 'Surya', 'Teguh', 'Umar', 'Vino', 'Wahyu', 'Yusuf', 'Zaki',
    ];

    private array $femaleFirst = [
        'Aulia', 'Bunga', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana', 'Intan',
        'Jasmine', 'Kirana', 'Laras', 'Maya', 'Nabila', 'Olivia', 'Putri', 'Rani',
        'Sari', 'Tania', 'Ulya', 'Vina', 'Wulan', 'Yuni', 'Zahra',
    ];

    private array $lastNames = [
        'Pratama', 'Saputra', 'Wijaya', 'Santoso', 'Nugroho', 'Hidayat', 'Kurniawan',
        'Setiawan', 'Ramadhan', 'Siregar', 'Manik', 'Puspita', 'Anggraini', 'Gunawan', 'Hartono',
    ];

    private array $teacherNames = [
        'Agus Setiawan, S.Pd.', 'Sri Wahyuni, S.Pd.', 'Bambang Prasetyo, S.Pd.',
        'Rina Marlina, S.Pd.', 'Dedi Kurniawan, S.Pd.', 'Nur Halimah, S.Pd.',
        'Eko Purnomo, S.Pd.', 'Lestari Handayani, S.Pd.', 'Agung Nugroho, S.Pd.',
        'Fitria Rahmawati, S.Pd.', 'Hadi Saputra, S.Pd.', 'Yulia Anggraeni, S.Pd.',
        'Rudi Hartono, S.Pd.', 'Melati Kusuma, S.Pd.', 'Firmansyah, S.Pd.',
    ];

    public function run(): void
    {
        if (Student::count() > 0) {
            $this->command->warn('Data siswa sudah ada. Dummy data tidak dijalankan agar data asli tidak tertimpa.');

            return;
        }

        $year = AcademicYear::where('is_active', true)->first();

        if (! $year) {
            $this->command->error('Tahun ajaran aktif belum ada. Jalankan DatabaseSeeder terlebih dahulu.');

            return;
        }

        $oldThreshold = Setting::getValue('wa_threshold');
        $oldAchievementEnabled = Setting::getValue('wa_achievement_enabled');

        // Matikan notifikasi WA sementara agar antrean tidak penuh saat seeding.
        Setting::setValue('wa_threshold', '999999', 'whatsapp');
        Setting::setValue('wa_achievement_enabled', '0', 'whatsapp');

        try {
            DB::transaction(function () use ($year) {
                $teachers = $this->createTeachers();
                $classes = $this->createClasses($year, $teachers);
                $violationCategories = ViolationCategory::where('is_active', true)->get();
                $achievementCategories = AchievementCategory::where('is_active', true)->get();

                for ($index = 1; $index <= 500; $index++) {
                    $student = $this->createStudent($year, $classes, $index);
                    $this->createParents($student, $index);

                    $violationCount = $this->weightedRandom([0, 0, 1, 1, 1, 2, 2, 3, 4, 5]);
                    for ($v = 0; $v < $violationCount; $v++) {
                        $category = $violationCategories->random();
                        $rule = PointRule::where('ruleable_type', ViolationCategory::class)
                            ->where('ruleable_id', $category->id)
                            ->where('academic_year_id', $year->id)
                            ->first();

                        if (! $rule) {
                            continue;
                        }

                        $date = now()->subDays(mt_rand(1, 180))->toDateString();

                        $violation = Violation::create([
                            'student_id' => $student->id,
                            'violation_category_id' => $category->id,
                            'academic_year_id' => $year->id,
                            'teacher_id' => $teachers[array_rand($teachers)],
                            'incident_date' => $date,
                            'description' => $this->violationDescription($category->name),
                            'evidence_path' => null,
                            'point_amount' => $rule->point_value,
                            'status' => 'aktif',
                        ]);

                        PointService::record([
                            'student_id' => $student->id,
                            'academic_year_id' => $year->id,
                            'type' => 'pelanggaran',
                            'balance_type' => 'pelanggaran',
                            'source_type' => Violation::class,
                            'source_id' => $violation->id,
                            'points' => $rule->point_value,
                            'description' => 'Pelanggaran: '.$category->name,
                        ]);
                    }

                    $achievementCount = $this->weightedRandom([0, 0, 1, 1, 2, 2, 3]);
                    for ($a = 0; $a < $achievementCount; $a++) {
                        $category = $achievementCategories->random();
                        $rule = PointRule::where('ruleable_type', AchievementCategory::class)
                            ->where('ruleable_id', $category->id)
                            ->where('academic_year_id', $year->id)
                            ->first();

                        if (! $rule) {
                            continue;
                        }

                        $achievement = Achievement::create([
                            'student_id' => $student->id,
                            'achievement_category_id' => $category->id,
                            'academic_year_id' => $year->id,
                            'teacher_id' => $teachers[array_rand($teachers)],
                            'achievement_date' => now()->subDays(mt_rand(1, 180))->toDateString(),
                            'description' => $this->achievementDescription($category->name, $category->level),
                            'documentation_path' => null,
                            'point_amount' => $rule->point_value,
                            'is_public' => true,
                        ]);

                        PointService::record([
                            'student_id' => $student->id,
                            'academic_year_id' => $year->id,
                            'type' => 'prestasi',
                            'balance_type' => 'prestasi',
                            'source_type' => Achievement::class,
                            'source_id' => $achievement->id,
                            'points' => $rule->point_value,
                            'description' => 'Prestasi: '.$category->name,
                        ]);
                    }
                }
            });
        } finally {
            Setting::setValue('wa_threshold', (string) $oldThreshold, 'whatsapp');
            Setting::setValue('wa_achievement_enabled', (string) $oldAchievementEnabled, 'whatsapp');
        }

        $this->command->info('Dummy data selesai: 500 siswa, '.Teacher::count().' guru, '
            .Violation::count().' pelanggaran, '.Achievement::count().' prestasi, '
            .User::whereHas('roles', fn ($q) => $q->where('name', 'guru'))->count().' akun guru.');
    }

    private function createTeachers(): array
    {
        $ids = [];

        foreach ($this->teacherNames as $index => $name) {
            $gender = $index % 2 === 0 ? 'L' : 'P';
            $email = 'guru'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'@poinsiswa.test';

            $teacher = Teacher::create([
                'nip' => '1980'.str_pad((string) ($index + 1), 10, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => $email,
                'phone' => '0813'.str_pad((string) (10000000 + $index * 111111), 8, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'address' => 'Tangerang Selatan',
                'is_active' => true,
            ]);

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('guru12345'),
                'teacher_id' => $teacher->id,
                'is_active' => true,
            ]);
            $user->assignRole('guru');

            $ids[] = $teacher->id;
        }

        return $ids;
    }

    private function createClasses(AcademicYear $year, array $teacherIds): array
    {
        $ids = [];

        foreach (['7A', '7B', '7C', '8A', '8B', '8C', '9A', '9B', '9C'] as $index => $name) {
            $class = Kelas::create([
                'academic_year_id' => $year->id,
                'name' => $name,
                'homeroom_teacher_id' => $teacherIds[$index % count($teacherIds)],
            ]);

            $ids[] = $class->id;
        }

        return $ids;
    }

    private function createStudent(AcademicYear $year, array $classIds, int $index): Student
    {
        $isMale = $index % 2 === 1;
        $firstName = $isMale
            ? $this->maleFirst[($index - 1) % count($this->maleFirst)]
            : $this->femaleFirst[($index - 1) % count($this->femaleFirst)];
        $lastName = $this->lastNames[($index - 1) % count($this->lastNames)];

        $birthYear = 2011 + (($index - 1) % 3);

        return Student::create([
            'class_id' => $classIds[($index - 1) % count($classIds)],
            'academic_year_id' => $year->id,
            'nisn' => '00'.str_pad((string) $index, 8, '0', STR_PAD_LEFT),
            'name' => $firstName.' '.$lastName.' '.$index,
            'gender' => $isMale ? 'L' : 'P',
            'birth_date' => $birthYear.'-'.str_pad((string) (($index % 12) + 1), 2, '0', STR_PAD_LEFT).'-'.str_pad((string) (($index % 27) + 1), 2, '0', STR_PAD_LEFT),
            'address' => 'Jl. Percontohan No. '.$index.', Tangerang Selatan',
            'status' => 'aktif',
        ]);
    }

    private function createParents(Student $student, int $index): void
    {
        $fatherName = $this->maleFirst[($index - 1) % count($this->maleFirst)].' '.$this->lastNames[($index - 1) % count($this->lastNames)];
        $motherName = $this->femaleFirst[($index - 1) % count($this->femaleFirst)].' '.$this->lastNames[($index - 1) % count($this->lastNames)];

        $fatherPhone = '0812'.str_pad((string) (10000000 + $index), 8, '0', STR_PAD_LEFT);
        $motherPhone = '0857'.str_pad((string) (10000000 + $index), 8, '0', STR_PAD_LEFT);

        $father = OrangTua::where('phone', $fatherPhone)->firstOrNew();
        $father->name = $fatherName;
        $father->phone = $fatherPhone;
        $father->address = 'Tangerang Selatan';
        $father->save();

        $student->orangTuas()->attach($father->id, ['relation' => 'ayah', 'is_primary' => true]);

        $mother = OrangTua::where('phone', $motherPhone)->firstOrNew();
        $mother->name = $motherName;
        $mother->phone = $motherPhone;
        $mother->address = 'Tangerang Selatan';
        $mother->save();

        $student->orangTuas()->attach($mother->id, ['relation' => 'ibu', 'is_primary' => false]);
    }

    private function violationDescription(string $category): string
    {
        $templates = [
            'Terlambat masuk' => 'Datang setelah bel masuk berbunyi, tanpa keterangan.',
            'Tidak mengerjakan tugas' => 'Tidak menyerahkan tugas sesuai batas waktu yang ditentukan.',
            'Membolos' => 'Tidak hadir di sekolah tanpa izin yang sah.',
            'Berkelahi' => 'Terlibat perkelahian dengan siswa lain.',
        ];

        return $templates[$category] ?? 'Pelanggaran tercatat oleh guru.';
    }

    private function achievementDescription(string $category, ?string $level): string
    {
        return 'Memperoleh '.$category.' tingkat '.($level ?: 'sekolah').' tahun ajaran berjalan.';
    }

    private function weightedRandom(array $values): int
    {
        return $values[array_rand($values)];
    }
}
