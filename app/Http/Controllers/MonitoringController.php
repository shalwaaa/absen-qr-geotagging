<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Holiday; // <-- Import Model Holiday
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        
        // --- CEK 1: HARI LIBUR SABTU & MINGGU ---
        if ($now->isWeekend()) {
            return view('admin.monitoring.holiday', [
                'reason' => 'Libur Akhir Pekan (' . $now->isoFormat('dddd') . ')',
                'date' => $now->translatedFormat('d F Y'),
                'icon' => 'fa-mug-hot'
            ]);
        }

        // --- CEK 2: LIBUR NASIONAL / MANUAL DARI DB ---
        $holiday = Holiday::whereDate('date', $now->toDateString())->first();
        if ($holiday) {
            return view('admin.monitoring.holiday', [
                'reason' => $holiday->title,
                'date' => $now->translatedFormat('d F Y'),
                'icon' => 'fa-umbrella-beach'
            ]);
        }

        // --- LANJUT KE LOGIC MONITORING ---
        $search = $request->input('search');
        $todayName = $now->isoFormat('dddd');

        $query = Schedule::with(['teacher', 'subject', 'classroom'])
            ->where('day', $todayName);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        $schedules = $query->orderBy('start_time', 'asc')->paginate(9)->withQueryString();

        $monitoringData = $schedules->map(function ($schedule) use ($now) {

            // Cek Meeting
            $meeting = Meeting::where('schedule_id', $schedule->id)
                ->whereDate('date', $now->toDateString())
                ->with('opener')
                ->first();

            // Cek Izin Guru
            $leave = \App\Models\LeaveRequest::where('student_id', $schedule->teacher_id)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $now->toDateString())
                ->whereDate('end_date', '>=', $now->toDateString())
                ->first();

            $start = Carbon::parse($schedule->start_time, 'Asia/Jakarta');
            $end   = Carbon::parse($schedule->end_time, 'Asia/Jakarta');
            $lateThreshold = $start->copy()->addMinutes(15);

            $status = ''; $badgeClass = ''; $keterangan = ''; $isUrgent = false;

            if ($meeting) {
                $studentCount = $meeting->attendances()
                    ->whereHas('student', fn($q) => $q->where('role', 'student'))
                    ->count();

                if ($studentCount > 0) {
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
                    $status = 'Sesi Kosong';
                    $badgeClass = 'bg-orange-100 text-orange-700 border-orange-200 animate-pulse';
                    $keterangan = 'Sesi dibuka, belum ada siswa';
                }

            } elseif ($leave) {
                $status = ($leave->type == 'sick') ? 'Sakit' : 'Izin';
                $badgeClass = 'bg-purple-100 text-purple-700 border-purple-200';
                $keterangan = 'Guru berhalangan (' . Str::limit($leave->reason, 20) . ')';
                $isUrgent = true;

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
                    $isUrgent = true;
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
                'is_urgent' => $isUrgent,
            ];
        });

        $monitoringData = $monitoringData->sort(function ($a, $b) {
            $priority = ['Terlambat' => 1, 'Sakit' => 2, 'Izin' => 2, 'Sesi Kosong' => 3, 'Belum Masuk' => 4, 'Digantikan' => 5, 'Hadir' => 6, 'Menunggu' => 7, 'Selesai (Tanpa Kabar)' => 8];
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