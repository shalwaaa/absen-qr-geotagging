<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DummyScheduleSeeder extends Seeder
{
    public function run()
    {
        // 1. BUAT DATA MATA PELAJARAN (JIKA BELUM ADA)
        $subjectsData = [
            ['name' => 'Matematika', 'code' => 'MTK'],
            ['name' => 'Bahasa Indonesia', 'code' => 'IND'],
            ['name' => 'Bahasa Inggris', 'code' => 'ING'],
            ['name' => 'Pendidikan Agama', 'code' => 'PAI'],
            ['name' => 'Pemrograman Web', 'code' => 'PWPB'],
            ['name' => 'Basis Data', 'code' => 'BASDAT'],
            ['name' => 'Desain Grafis', 'code' => 'DG'],
            ['name' => 'Jaringan Dasar', 'code' => 'JARDAS'],
        ];

        foreach ($subjectsData as $s) {
            Subject::firstOrCreate(
                ['code' => $s['code']],
                ['name' => $s['name']]
            );
        }

        $this->command->info('Data Mata Pelajaran berhasil dibuat.');

        // 2. AMBIL DATA GURU (PAK BUDI)
        // Kita cari user yang namanya ada "Budi" dan rolenya guru
        $pakBudi = User::where('role', 'teacher')
                        ->where('name', 'LIKE', '%Budi%')
                        ->first();

        // Jika Pak Budi tidak ada, ambil guru pertama saja
        if (!$pakBudi) {
            $pakBudi = User::where('role', 'teacher')->first();
            if (!$pakBudi) {
                $this->command->error('Tidak ada data Guru. Buat user guru dulu!');
                return;
            }
        }
        
        $this->command->info("Membuat jadwal untuk guru: {$pakBudi->name}");

        // 3. AMBIL KELAS (Acak)
        $classrooms = Classroom::all();
        if ($classrooms->count() < 3) {
            $this->command->error('Minimal butuh 3 kelas untuk demo ini.');
            return;
        }

        // 4. BUAT JADWAL SENIN - JUMAT
        // Hapus jadwal lama Pak Budi biar gak duplikat
        Schedule::where('teacher_id', $pakBudi->id)->delete();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $timeSlots = [
            ['start' => '07:00', 'end' => '09:00'],
            ['start' => '09:15', 'end' => '11:15'],
            ['start' => '13:00', 'end' => '15:00'],
        ];

        foreach ($days as $dayIndex => $day) {
            // Setiap hari Pak Budi mengajar di 2-3 sesi
            foreach ($timeSlots as $slotIndex => $time) {
                
                // Rotasi Kelas & Mapel biar variatif
                // Pakai operator Modulo (%) agar index berputar
                $class = $classrooms[($dayIndex + $slotIndex) % $classrooms->count()];
                
                // Ambil mapel random dari database
                $subject = Subject::inRandomOrder()->first();

                Schedule::create([
                    'teacher_id' => $pakBudi->id,
                    'classroom_id' => $class->id,
                    'subject_id' => $subject->id,
                    'day' => $day,
                    'start_time' => $time['start'],
                    'end_time' => $time['end']
                ]);
            }
        }

        $this->command->info('Sukses! Jadwal Pak Budi sudah penuh Senin-Jumat.');
    }
}