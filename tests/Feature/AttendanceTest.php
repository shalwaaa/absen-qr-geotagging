<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Meeting;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttendanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_absen_berhasil()
    {
        // 1. Buat Data Kelas
        $classroom = Classroom::create([
            'name' => 'Kelas Test ' . rand(1, 999),
            'grade_level' => 10,
            'latitude' => '-6.825125', 
            'longitude' => '107.141228',
            'radius_meters' => 1000
        ]);

        // 2. Buat Data Guru (User)
        $teacher = User::create([
            'name' => 'Guru Test',
            'email' => 'guru' . rand(1, 999) . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active'
        ]);

        // 3. Buat Data Mapel
        $subject = Subject::create(['name' => 'Mapel Test', 'code' => 'MT' . rand(1, 99)]);

        // 4. Buat Data Jadwal (Schedule)
        // Menambahkan kolom 'day' dan 'day_of_week' agar sesuai database kamu
        $schedule = Schedule::create([
            'classroom_id' => $classroom->id,
            'subject_id'   => $subject->id,
            'teacher_id'   => $teacher->id,
            'day'          => now()->translatedFormat('l'), // Mengisi nama hari (misal: Monday/Senin)
            'day_of_week'  => now()->dayOfWeek,            // Mengisi angka hari (1-7)
            'start_time'   => '07:00',
            'end_time'     => '10:00'
        ]);

        // 5. Buat Data Meeting
        $meeting = Meeting::create([
            'schedule_id' => $schedule->id,
            'date'        => now()->toDateString(),
            'qr_token'    => 'TOKEN-TEST-' . rand(1, 999),
            'is_active'   => true,
            'topic'       => 'Test PHPUnit'
        ]);

        // 6. Buat User Siswa
        $user = User::create([
            'name'         => 'Siswa Tester',
            'email'        => 'siswa' . rand(1, 999) . '@test.com',
            'password'     => Hash::make('password'),
            'role'         => 'student',
            'status'       => 'active',
            'classroom_id' => $classroom->id
        ]);

        // 7. Jalankan Request Absen
        $response = $this->actingAs($user)->postJson('/attendance', [
            "qr_token"  => $meeting->qr_token,
            "latitude"  => "-6.825125",
            "longitude" => "107.141228",
        ]);

        // Jika masih gagal, hapus // baris di bawah untuk melihat pesan error dari server
        // $response->dump();

        $response->assertStatus(200);
    }

    public function test_absen_gagal()
    {
        $user = User::where('status', 'active')->first() ?: User::create([
            'name'     => 'User Gagal',
            'email'    => 'gagal' . rand(1, 999) . '@test.com',
            'password' => Hash::make('password'),
            'status'   => 'active'
        ]);

        $response = $this->actingAs($user)->postJson('/attendance', []);

        $response->assertStatus(422);
    }
}