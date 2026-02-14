<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $search = $request->input('search');
        $todayName = Carbon::now('Asia/Jakarta')->isoFormat('dddd');
        $now = Carbon::now('Asia/Jakarta');

        $query = Schedule::with(['teacher', 'subject', 'classroom'])
            ->where('day', $todayName);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        $schedules = $query->orderBy('start_time', 'asc')->get();

        $monitoringData = $schedules->map(function ($schedule) use ($now) {

            // 1. Cek Meeting (Apakah sudah buka kelas?)
            $meeting = Meeting::where('schedule_id', $schedule->id)
                ->whereDate('date', $now->toDateString())
                ->with('opener')
                ->first();

            // 2. Cek Izin/Sakit Guru (Apakah guru ini punya izin hari ini?)
            $leave = \App\Models\LeaveRequest::where('student_id', $schedule->teacher_id) // Ingat: user_id guru ada di kolom student_id
                ->where('status', 'approved') // Harus yang sudah disetujui Kepsek/Admin
                ->whereDate('start_date', '<=', $now->toDateString())
                ->whereDate('end_date', '>=', $now->toDateString())
                ->first();

            $start = Carbon::parse($schedule->start_time, 'Asia/Jakarta');
            $end   = Carbon::parse($schedule->end_time, 'Asia/Jakarta');
            $lateThreshold = $start->copy()->addMinutes(15);

            // ===== LOGIKA STATUS PRIORITAS =====
            $status = '';
            $badgeClass = '';
            $keterangan = '';
            $isUrgent = false; // Trigger tombol panggil piket

            if ($meeting) {
                // KASUS A: Meeting Sudah Ada
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
                // KASUS B: Guru Izin/Sakit (Resmi)
                // Ini harus dianggap URGENT agar admin panggil piket
                $status = ($leave->type == 'sick') ? 'Sakit' : 'Izin';
                $badgeClass = 'bg-purple-100 text-purple-700 border-purple-200';
                $keterangan = 'Guru berhalangan (' . Str::limit($leave->reason, 20) . ')';
                $isUrgent = true; // Tetap urgent karena kelas kosong!

            } else {
                // KASUS C: Tidak ada kabar
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

        // Sorting
        $monitoringData = $monitoringData->sort(function ($a, $b) {
            // Prioritas sorting: Terlambat/Sakit/Izin (Urgent) paling atas
            $priority = [
                'Terlambat' => 1,
                'Sakit' => 2,
                'Izin' => 2,
                'Sesi Kosong' => 3,
                'Belum Masuk' => 4,
                'Digantikan' => 5,
                'Hadir' => 6,
                'Menunggu' => 7,
                'Selesai (Tanpa Kabar)' => 8
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