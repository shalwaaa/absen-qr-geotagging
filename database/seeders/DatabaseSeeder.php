<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kelas Contoh (Lokasi set sembarang)
        $kelas = Classroom::create([
            'name' => 'XII-RPL-1',
            'latitude' => -6.200000, // Contoh koordinat Jakarta
            'longitude' => 106.816666,
            'radius_meters' => 100,
        ]);

        // 2.ADMIN
        User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3.Akun GURU
        User::create([
            'name' => 'Pak Budi Guru',
            'email' => 'guru@sekolah.id',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'nip_nis' => '19850101',
        ]);

        // 4.Akun SISWA 
        User::create([
            'name' => 'Ani Siswa',
            'email' => 'siswa@sekolah.id',
            'password' => Hash::make('password'),
            'role' => 'student',
            'nip_nis' => '12345',
            'classroom_id' => $kelas->id, // Masuk kelas XII-RPL-1
        ]);
    }
}