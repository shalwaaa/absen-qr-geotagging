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
        $todayDate = Carbon::now('Asia/Jakarta')->toDateString();
        $todayName = Carbon::now('Asia/Jakarta')->isoFormat('dddd'); // Senin, Selasa...

        // Query dasar jadwal hari ini
        $query = Schedule::with(['teacher', 'subject', 'classroom'])
            ->where('day', $todayName)
            ->whereDate('created_at', $todayDate);

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

            $meeting = Meeting::where('schedule_id', $schedule->id)
                ->whereDate('date', $now->toDateString())
                ->with('opener')
                ->first();

            $start = Carbon::parse($schedule->start_time, 'Asia/Jakarta');
            $end   = Carbon::parse($schedule->end_time, 'Asia/Jakarta');

            $lateThreshold = $start->copy()->addMinutes(15);

            // ===== LOGIKA STATUS =====
            if ($meeting) {
                if ($meeting->opened_by == $schedule->teacher_id) {
                    $status = 'Hadir';
                    $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                    $keterangan = 'Mengajar di kelas';
                } else {
                    $status = 'Digantikan';
                    $badgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                    $keterangan = 'Oleh: ' . ($meeting->opener->name ?? 'Guru Piket');
                }
            } else {
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

        // Yang terlambat paling atas
        $monitoringData = $monitoringData->sortByDesc('is_urgent');

        return view('admin.monitoring.index', [
            'monitoringData' => $monitoringData,
            'today' => $todayName,
            'search' => $search,
        ]);

    }


    // Fungsi untuk Admin memanggil Guru Piket
    public function panggilPiket($id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Toggle status (kalau 0 jadi 1, kalau 1 jadi 0)
        $schedule->request_piket = !$schedule->request_piket;
        $schedule->save();

        $status = $schedule->request_piket ? 'memanggil' : 'membatalkan panggilan';
        return back()->with('success', "Berhasil $status Guru Piket untuk kelas ini.");
    }
}