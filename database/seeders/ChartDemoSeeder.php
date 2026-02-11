<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ChartDemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil Semua Kelas 10 yang punya siswa
        $classrooms = Classroom::where('grade_level', 10)
                        ->whereHas('students') // Hanya yang ada siswanya
                        ->get();

        $teacher = User::where('role', 'teacher')->first();
        $subject = Subject::first();

        // Validasi
        if ($classrooms->isEmpty()) {
            $this->command->error('Tidak ada Kelas 10 yang memiliki siswa. Pastikan sudah Sync Data!');
            return;
        }
        if (!$teacher || !$subject) {
            // Buat dummy guru/mapel darurat jika kosong
            $teacher = User::firstOrCreate(
                ['email' => 'guru.dummy@sekolah.id'],
                ['name' => 'Guru Dummy', 'password' => bcrypt('password'), 'role' => 'teacher', 'nip_nis' => 'G-DUMMY']
            );
            $subject = Subject::firstOrCreate(['name' => 'Mapel Umum', 'code' => 'UMUM']);
        }

        $this->command->info("Ditemukan " . $classrooms->count() . " Kelas Tingkat 10. Memulai generate data...");

        DB::beginTransaction();

        try {
            foreach ($classrooms as $class) {
                // Tentukan "Tingkat Kerajinan" kelas ini secara acak (60% s.d 98%)
                // Ini biar grafiknya terlihat beda-beda (ada yg naik, ada yg turun)
                $baseAttendanceRate = rand(60, 98); 
                
                $this->command->info("Processing: {$class->name} (Rate: ~{$baseAttendanceRate}%)");

                $students = User::where('classroom_id', $class->id)->get();

                // Buat Jadwal Dummy untuk kelas ini
                $schedule = Schedule::firstOrCreate([
                    'classroom_id' => $class->id,
                    'day' => 'Senin',
                ], [
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'start_time' => '07:00:00',
                    'end_time' => '09:00:00',
                ]);

                // Loop 6 Bulan Terakhir
                for ($m = 5; $m >= 0; $m--) {
                    
                    // Buat 4 pertemuan per bulan (Seminggu sekali)
                    for ($week = 0; $week < 4; $week++) {
                        
                        $date = Carbon::now()->subMonths($m)->startOfMonth()->addWeeks($week);
                        if ($date->isWeekend()) $date->addDays(2); // Skip weekend

                        // Buat Meeting
                        $meeting = Meeting::create([
                            'schedule_id' => $schedule->id,
                            'date' => $date->format('Y-m-d'),
                            'qr_token' => Str::random(40),
                            'is_active' => false,
                            'opened_by' => $teacher->id
                        ]);

                        // Isi Absen Siswa
                        $attendancesData = [];
                        foreach ($students as $student) {
                            // Random status per siswa berdasarkan rate kelas
                            $luck = rand(1, 100);
                            
                            if ($luck <= $baseAttendanceRate) {
                                $status = 'present';
                            } elseif ($luck <= $baseAttendanceRate + 5) {
                                $status = 'sick';
                            } elseif ($luck <= $baseAttendanceRate + 10) {
                                $status = 'permission';
                            } else {
                                $status = 'alpha';
                            }

                            // Alpha tidak masuk database (atau masuk jika kamu mau)
                            // Disini kita masukkan Present, Sick, Permission
                            if ($status != 'alpha') {
                                $attendancesData[] = [
                                    'meeting_id' => $meeting->id,
                                    'student_id' => $student->id,
                                    'status' => $status,
                                    'scan_time' => $date->copy()->setTime(7, rand(0, 59), 0),
                                    'latitude_student' => -6.200000,
                                    'longitude_student' => 106.816666,
                                    'distance_meters' => rand(1, 30),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }

                        // Insert Batch biar cepat
                        if (!empty($attendancesData)) {
                            Attendance::insert($attendancesData);
                        }
                    }
                }
            }
            
            DB::commit();
            $this->command->info('SUKSES! Data dummy Kelas 10 berhasil dibuat.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal: ' . $e->getMessage());
        }
    }
}