<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Holiday; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MeetingController extends Controller
{
    // Cek Libur (Weekend + Database)
    private function checkHoliday()
    {
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');

        // 1. Cek Weekend
        if ($now->isWeekend()) {
            return [
                'is_holiday' => true,
                'view' => view('teacher.holiday', [
                    'reason' => 'Libur Akhir Pekan (' . $now->isoFormat('dddd') . ')',
                    'icon' => 'fa-mug-hot'
                ])
            ];
        }

        // 2. Cek Libur Database
        $holiday = Holiday::whereDate('date', $now->toDateString())->first();
        if ($holiday) {
            return [
                'is_holiday' => true,
                'view' => view('teacher.holiday', [
                    'reason' => $holiday->title,
                    'icon' => 'fa-umbrella-beach'
                ])
            ];
        }

        return ['is_holiday' => false];
    }

    // --- DASHBOARD GURU ---
     public function index()
    {
        // 1. Cek Libur (Logic sebelumnya)
        $holidayCheck = $this->checkHoliday();
        if ($holidayCheck['is_holiday']) return $holidayCheck['view'];

        Carbon::setLocale('id');
        $today = Carbon::now('Asia/Jakarta')->isoFormat('dddd');

        // 2. LOGIKA MINGGU GANJIL/GENAP (PERBAIKAN)

        // Ambil nomor minggu saat ini (1 s.d 52)
        $weekNumber = Carbon::now('Asia/Jakarta')->weekOfYear; 
        
        // Tentukan tipe minggu: 'even' untuk genap, 'odd' untuk ganjil
        $currentWeekType = ($weekNumber % 2 == 0) ? 'even' : 'odd';
        
        // Buat variabel untuk menampilkan info minggu di dashboard
        $weekInfo = ($currentWeekType == 'odd') ? 'Minggu Ganjil' : 'Minggu Genap';
        // ==========================================

        // 3. Ambil Jadwal Guru
        $schedules = Schedule::with(['subject', 'classroom'])
            ->where('teacher_id', Auth::id())
            ->where('day', $today)
            ->whereIn('week_type', ['all', $currentWeekType]) 
            ->orderBy('start_time')
            ->get();

        foreach ($schedules as $s) {
            $s->today_meeting = Meeting::where('schedule_id', $s->id)
                                       ->whereDate('date', Carbon::today('Asia/Jakarta'))
                                       ->first();
        }

        // 4. Kirim variabel $weekInfo ke View
        return view('teacher.dashboard', compact('schedules', 'today', 'weekInfo'));
    }

    // --- DASHBOARD PIKET ---
    public function piketIndex()
    {
        // Cek Libur
        $holidayCheck = $this->checkHoliday();
        if ($holidayCheck['is_holiday']) return $holidayCheck['view'];

        if (!Auth::user()->is_piket && Auth::user()->role != 'admin') {
            abort(403, 'Anda bukan Guru Piket.');
        }

        Carbon::setLocale('id');
        $today = Carbon::now('Asia/Jakarta')->isoFormat('dddd');

        // Logika Minggu Ganjil/Genap
        $weekNumber = Carbon::now('Asia/Jakarta')->weekOfYear; 
        $currentWeekType = ($weekNumber % 2 == 0) ? 'even' : 'odd';
        $weekInfo = ($currentWeekType == 'odd') ? 'Minggu Ganjil' : 'Minggu Genap';

        // Ambil SEMUA jadwal hari ini yang sesuai tipe minggu
        $schedules = Schedule::with(['teacher', 'subject', 'classroom'])
            ->where('day', $today)
            ->whereIn('week_type', ['all', $currentWeekType]) 
            ->orderBy('start_time')
            ->get();

        foreach ($schedules as $s) {
            $s->today_meeting = Meeting::where('schedule_id', $s->id)
                                       ->whereDate('date', Carbon::today('Asia/Jakarta'))
                                       ->first();
        }

        return view('teacher.piket', compact('schedules', 'today', 'weekInfo'));
    }

    public function store(Request $request)
    {
        $request->validate(['schedule_id' => 'required|exists:schedules,id']);
        
        $meeting = Meeting::where('schedule_id', $request->schedule_id)
                          ->whereDate('date', Carbon::today())
                          ->first();

        $openerId = Auth::id();
        $schedule = Schedule::with('classroom')->findOrFail($request->schedule_id);

        // Pastikan yang membuka adalah guru yang bersangkutan atau admin
        if ($meeting) {
            $meeting->update([
                'is_active' => true,
                'opened_by' => $openerId,
                'qr_token' => $meeting->qr_token ?? Str::random(40) 
            ]);
        } else {
            // Buat meeting baru jika belum ada untuk hari ini
            $meeting = Meeting::create([
                'schedule_id' => $request->schedule_id,
                'date' => Carbon::today(),
                'qr_token' => Str::random(40),
                'is_active' => true,
                'opened_by' => $openerId,
            ]);
        }

        // Pastikan guru yang membuka juga tercatat hadir (untuk keperluan rekap)
        $guruHadir = Attendance::where('meeting_id', $meeting->id)
                               ->where('student_id', $openerId)
                               ->exists();

        // Jika belum tercatat hadir, buat record kehadiran untuk guru tersebut
        if (!$guruHadir) {
            Attendance::create([
                'meeting_id' => $meeting->id,
                'student_id' => $openerId,
                'status' => 'present',
                'latitude_student' => $schedule->classroom->latitude,
                'longitude_student' => $schedule->classroom->longitude,
                'distance_meters' => 0,
                'scan_time' => now(),
            ]);
        }

        return redirect()->route('meetings.show', $meeting->id);
    }

    public function show($id)
    {
        // Ambil data meeting beserta relasi yang diperlukan
        $meeting = Meeting::with(['schedule.subject', 'schedule.classroom', 'attendances.student', 'opener'])
                          ->findOrFail($id);
        
        $user = Auth::user();

        // Pastikan hanya guru yang membuka, guru yang bersangkutan, atau admin yang bisa melihat detail meeting
        if ($meeting->schedule->teacher_id != $user->id && $user->role != 'admin' && $meeting->opened_by != $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ini.');
        }

        return view('teacher.meeting_show', compact('meeting'));
    }

    // Fitur tambahan: Toggle Status Aktif Meeting
    public function toggleStatus($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->is_active = !$meeting->is_active;
        $meeting->save();
        return back();
    }

    // Fitur tambahan: Regenerate QR Code (dynamic QR)
    public function regenerateQr($id)
    {
        $meeting = Meeting::findOrFail($id);
        $user = Auth::user();
        
        if ($meeting->schedule->teacher_id != $user->id && $user->role != 'admin' && $meeting->opened_by != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $newToken = Str::random(40);
        $meeting->update(['qr_token' => $newToken]);
        $qrCodeHtml = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(280)->margin(1)->generate($newToken);

        return response()->json([
            'status' => 'success',
            'qr_html' => (string) $qrCodeHtml
        ]);
    }
}