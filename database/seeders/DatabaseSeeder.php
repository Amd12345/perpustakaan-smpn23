<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => env('ADMIN_USERNAME', 'admin')],
            [
                'name' => 'Administrator',
                'email' => 'admin@perpustakaan.sch.id',
                'password' => env('ADMIN_PASSWORD', 'admin123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
