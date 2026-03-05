<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $kelas = Classroom::firstOrCreate(
            ['name' => 'XII-RPL-3'], // Cek berdasarkan nama
            [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius_meters' => 50,
                'grade_level' => 12
            ]
        );

        // 2. ADMIN
        User::firstOrCreate(
            ['email' => 'admin@sekolah.id'], // Kuncinya: Cek email ini
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 3. Akun GURU
        User::firstOrCreate(
            ['email' => 'guru@sekolah.id'],
            [
                'name' => 'Pak Budi Guru',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'nip_nis' => '19850101',
            ]
        );

        // 4. Akun SISWA
        User::firstOrCreate(
            ['email' => 'siswa@sekolah.id'],
            [
                'name' => 'Ani Siswa',
                'password' => Hash::make('password'),
                'role' => 'student',
                'nip_nis' => '12345',
                'classroom_id' => $kelas->id, 
                'status' => 'active'
            ]
        );
    }
}