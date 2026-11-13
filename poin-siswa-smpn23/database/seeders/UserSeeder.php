<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('SEED_PASSWORD', 'admin12345');

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@poinsiswa.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'is_active' => true,
            ],
        );
        $superAdmin->syncRoles('super_admin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@poinsiswa.test'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make($password),
                'is_active' => true,
            ],
        );
        $admin->syncRoles('admin');

        $kepsekTeacher = Teacher::firstOrCreate(
            ['email' => 'kepsek@poinsiswa.test'],
            [
                'nip' => '196501011990031001',
                'name' => 'Drs. Budi Santoso, M.Pd.',
                'phone' => '081234567890',
                'gender' => 'L',
                'is_active' => true,
            ],
        );

        $kepsek = User::firstOrCreate(
            ['email' => 'kepsek@poinsiswa.test'],
            [
                'name' => 'Drs. Budi Santoso, M.Pd.',
                'password' => Hash::make($password),
                'teacher_id' => $kepsekTeacher->id,
                'is_active' => true,
            ],
        );
        $kepsek->syncRoles('kepala_sekolah');

        $guruTeacher = Teacher::firstOrCreate(
            ['email' => 'guru@poinsiswa.test'],
            [
                'nip' => '198001012010012002',
                'name' => 'Siti Rahmawati, S.Pd.',
                'phone' => '081234567891',
                'gender' => 'P',
                'is_active' => true,
            ],
        );

        $guru = User::firstOrCreate(
            ['email' => 'guru@poinsiswa.test'],
            [
                'name' => 'Siti Rahmawati, S.Pd.',
                'password' => Hash::make($password),
                'teacher_id' => $guruTeacher->id,
                'is_active' => true,
            ],
        );
        $guru->syncRoles('guru');
    }
}
