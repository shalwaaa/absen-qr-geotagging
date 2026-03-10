<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DummyTeacherAttendanceSeeder extends Seeder
{
    public function run()
    {
        // Set waktu untuk 2 bulan terakhir sampai hari ini
        $startDate = Carbon::now('Asia/Jakarta')->subMonths(2)->startOfMonth();
        $endDate = Carbon::now('Asia/Jakarta');

        $this->command->info("Mengisi data kehadiran guru dari {$startDate->format('d/m/Y')} s/d {$endDate->format('d/m/Y')}...");

        // Ambil semua guru yang PUNYA jadwal mengajar
        $teachers = User::where('role', 'teacher')->whereHas('schedules')->with('schedules.classroom')->get();

        if ($teachers->isEmpty()) {
            $this->command->error('Tidak ada guru yang memiliki jadwal pelajaran. Buat jadwal dulu!');
            return;
        }

        // Ambil tanggal libur nasional ke dalam array biar cepat dicek
        $holidays = Holiday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                           ->pluck('date')
                           ->map(fn($d) => $d->format('Y-m-d'))
                           ->toArray();

        $period = CarbonPeriod::create($startDate, $endDate);

        DB::beginTransaction();

        try {
            $countHadir = 0;
            $countIzin = 0;

            foreach ($teachers as $teacher) {
                // Loop setiap hari dalam 2 bulan
                foreach ($period as $date) {
                    $dateString = $date->format('Y-m-d');

                    // 1. SKIP Libur (Sabtu, Minggu, dan Libur Nasional)
                    if ($date->isWeekend() || in_array($dateString, $holidays)) {
                        continue;
                    }

                    $dayName = $date->locale('id')->isoFormat('dddd');
                    
                    // 2. Cari jadwal guru ini di hari tersebut
                    $dailySchedules = $teacher->schedules->where('day', $dayName);

                    if ($dailySchedules->count() > 0) {
                        
                        // 3. Tentukan Nasib Guru Hari Ini (Gacha)
                        // 1-70 = Hadir, 71-77 = Sakit, 78-85 = Izin, 86-100 = Alpha
                        $chance = rand(1, 100);

                        if ($chance <= 70) {
                            // --- SKENARIO HADIR ---
                            foreach ($dailySchedules as $schedule) {
                                // Guru membuka kelas
                                $meeting = Meeting::firstOrCreate([
                                    'schedule_id' => $schedule->id,
                                    'date' => $dateString,
                                ],[
                                    'qr_token' => Str::random(40),
                                    'is_active' => false, // Anggap sesi sudah ditutup karena masa lalu
                                    'opened_by' => $teacher->id
                                ]);

                                // Absen guru tercatat
                                Attendance::firstOrCreate([
                                    'meeting_id' => $meeting->id,
                                    'student_id' => $teacher->id, // ID Guru
                                ],[
                                    'status' => 'present',
                                    'scan_time' => $date->copy()->setTime(rand(7, 8), rand(0, 59), 0),
                                    'latitude_student' => $schedule->classroom->latitude ?? -6.2,
                                    'longitude_student' => $schedule->classroom->longitude ?? 106.8,
                                    'distance_meters' => 0
                                ]);
                            }
                            $countHadir++;

                        } elseif ($chance <= 85) {
                            // --- SKENARIO SAKIT / IZIN ---
                            $type = ($chance <= 77) ? 'sick' : 'permission';

                            LeaveRequest::firstOrCreate([
                                'student_id' => $teacher->id, // ID Guru
                                'start_date' => $dateString,
                            ],[
                                'classroom_id' => null,
                                'type' => $type,
                                'end_date' => $dateString,
                                'reason' => "Simulasi Data Dummy - {$type}",
                                'status' => 'approved', // Langsung di-approve kepsek
                                'notes' => 'Di-approve otomatis oleh sistem dummy'
                            ]);
                            $countIzin++;

                        } else {
                            // --- SKENARIO ALPHA ---
                            // Tidak melakukan apa-apa, Laporan PDF akan mendeteksinya sebagai Alpha
                            // karena jadwal ada, tapi Meeting/Attendance dan LeaveRequest kosong.
                        }
                    }
                }
            }

            DB::commit();
            $this->command->info("Selesai! Berhasil membuat {$countHadir} hari Hadir dan {$countIzin} hari Izin/Sakit untuk para guru.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Gagal membuat dummy data: " . $e->getMessage());
        }
    }
}