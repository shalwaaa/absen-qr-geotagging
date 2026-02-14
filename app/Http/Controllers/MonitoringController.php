<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $search = $request->input('search');
        $todayName = Carbon::now('Asia/Jakarta')->isoFormat('dddd'); // Senin, Selasa...
        
        // Query dasar jadwal hari ini (Berdasarkan Hari, bukan tanggal buat jadwal)
        $query = Schedule::with(['teacher', 'subject', 'classroom'])
            ->where('day', $todayName);

        // Fitur search guru / mapel
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher', function ($teacherQuery) use ($search) {
                    $teacherQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('subject', function ($subjectQuery) use ($search) {
                    $subjectQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $schedules = $query->orderBy('start_time', 'asc')->get();

        // ===== Monitoring Status =====
        $now = Carbon::now('Asia/Jakarta');

        $monitoringData = $schedules->map(function ($schedule) use ($now) {

            // Ambil meeting beserta relasi absensi
            $meeting = Meeting::where('schedule_id', $schedule->id)
                ->whereDate('date', $now->toDateString())
                ->with('opener')
                ->first();

            $start = Carbon::parse($schedule->start_time, 'Asia/Jakarta');
            $end   = Carbon::parse($schedule->end_time, 'Asia/Jakarta');

            $lateThreshold = $start->copy()->addMinutes(15);

            // ===== LOGIKA STATUS =====
            $status = '';
            $badgeClass = '';
            $keterangan = '';
            
            if ($meeting) {
                // [BARU] Hitung jumlah siswa yang sudah scan di sesi ini
                // Kita filter whereHas 'student' role 'student' untuk memastikan bukan guru yg kehitung
                $studentCount = $meeting->attendances()
                    ->whereHas('student', function($q) {
                        $q->where('role', 'student');
                    })
                    ->count();

                // Cek Validasi Kehadiran
                if ($studentCount > 0) {
                    // VALID: Ada siswa yang scan
                    if ($meeting->opened_by == $schedule->teacher_id) {
                        $status = 'Hadir';
                        $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                        $keterangan = 'KBM Berjalan (' . $studentCount . ' Siswa)';
                    } else {
                        $status = 'Digantikan';
                        $badgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                        $keterangan = 'Oleh: ' . ($meeting->opener->name ?? 'Guru Piket');
                    }
                } else {
                    // INVALID: Sesi dibuka tapi belum ada siswa
                    $status = 'Sesi Kosong';
                    $badgeClass = 'bg-orange-100 text-orange-700 border-orange-200 animate-pulse';
                    $keterangan = 'Sesi dibuka, belum ada siswa scan';
                }

            } else {
                // Sesi BELUM dibuka
                if ($now < $start) {
                    $status = 'Menunggu';
                    $badgeClass = 'bg-gray-100 text-gray-500 border-gray-200';
                    $keterangan = 'Belum waktunya';
                } elseif ($now <= $lateThreshold) {
                    $status = 'Belum Masuk';
                    $badgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                    $keterangan = 'Segera hubungi guru';
                } elseif ($now <= $end) {
                    $status = 'Terlambat';
                    $badgeClass = 'bg-red-100 text-red-700 border-red-200 animate-pulse';
                    $keterangan = '🚨 PERLU GURU PIKET!';
                } else {
                    $status = 'Selesai (Tanpa Kabar)';
                    $badgeClass = 'bg-gray-200 text-gray-600 border-gray-300';
                    $keterangan = 'Sesi terlewat';
                }
            }

            return (object) [
                'schedule' => $schedule,
                'status' => $status,
                'badge_class' => $badgeClass,
                'keterangan' => $keterangan,
                'is_urgent' => ($status === 'Terlambat'),
            ];
        });

        // Sorting: Terlambat -> Sesi Kosong -> Lainnya
        $monitoringData = $monitoringData->sort(function ($a, $b) {
            $priority = [
                'Terlambat' => 1,
                'Sesi Kosong' => 2, // Prioritas kedua untuk dicek admin
                'Belum Masuk' => 3,
                'Digantikan' => 4,
                'Hadir' => 5,
                'Menunggu' => 6,
                'Selesai (Tanpa Kabar)' => 7
            ];
            
            $valA = $priority[$a->status] ?? 99;
            $valB = $priority[$b->status] ?? 99;
            
            return $valA <=> $valB;
        });

        return view('admin.monitoring.index', [
            'monitoringData' => $monitoringData,
            'today' => $todayName,
            'search' => $search,
        ]);
    }

    public function panggilPiket($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->request_piket = !$schedule->request_piket;
        $schedule->save();

        $status = $schedule->request_piket ? 'memanggil' : 'membatalkan panggilan';
        return back()->with('success', "Berhasil $status Guru Piket untuk kelas ini.");
    }
}