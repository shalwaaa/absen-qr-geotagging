<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Schedule;
use App\Models\Attendance; // Jangan lupa import ini
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MeetingController extends Controller
{
    // 1. Dashboard Guru (Hanya Jadwal Dia Sendiri)
    public function index()
    {
        Carbon::setLocale('id');
        $today = Carbon::now()->isoFormat('dddd');

        $schedules = Schedule::with(['subject', 'classroom'])
            ->where('teacher_id', Auth::id()) // Hanya jadwal dia
            ->where('day', $today)
            ->get();

        foreach ($schedules as $s) {
            $s->today_meeting = Meeting::where('schedule_id', $s->id)
                                       ->whereDate('date', Carbon::today())
                                       ->first();
        }

        return view('teacher.dashboard', compact('schedules', 'today'));
    }

    // 2. Dashboard GURU PIKET (Melihat SEMUA Jadwal Hari Ini)
    public function piketIndex()
    {
        // Cek apakah user punya akses piket?
        if (!Auth::user()->is_piket && Auth::user()->role != 'admin') {
            abort(403, 'Anda bukan Guru Piket.');
        }

        Carbon::setLocale('id');
        $today = Carbon::now()->isoFormat('dddd');

        // Ambil SEMUA jadwal hari ini
        $schedules = Schedule::with(['teacher', 'subject', 'classroom'])
            ->where('day', $today)
            ->orderBy('start_time')
            ->get();

        foreach ($schedules as $s) {
            $s->today_meeting = Meeting::where('schedule_id', $s->id)
                                       ->whereDate('date', Carbon::today())
                                       ->first();
        }

        return view('teacher.piket', compact('schedules', 'today'));
    }

    // 3. Buka Kelas & AUTO ABSEN GURU
public function store(Request $request)
    {
        $request->validate(['schedule_id' => 'required|exists:schedules,id']);

        // 1. Cek apakah Meeting sudah ada hari ini (Entah dibuat Guru atau Otomatis Wali Kelas)
        $meeting = Meeting::where('schedule_id', $request->schedule_id)
                          ->whereDate('date', Carbon::today())
                          ->first();

        $openerId = Auth::id();
        $schedule = Schedule::with('classroom')->findOrFail($request->schedule_id);

        if ($meeting) {
            // JIKA MEETING SUDAH ADA (Misal dari Auto-Create Wali Kelas)
            // Kita tinggal aktifkan saja dan set siapa yang membukanya sekarang
            $meeting->update([
                'is_active' => true,
                'opened_by' => $openerId,
                // Jika token belum ada (karena auto create), kita generate sekarang
                'qr_token' => $meeting->qr_token ?? Str::random(40) 
            ]);
        } else {
            // JIKA BELUM ADA, BUAT BARU
            $meeting = Meeting::create([
                'schedule_id' => $request->schedule_id,
                'date' => Carbon::today(),
                'qr_token' => Str::random(40),
                'is_active' => true,
                'opened_by' => $openerId,
            ]);
        }

        // 2. AUTO ABSEN GURU (Cek dulu biar gak double absen guru)
        $guruHadir = Attendance::where('meeting_id', $meeting->id)
                               ->where('student_id', $openerId)
                               ->exists();

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
    
    // ... method show dan toggleStatus biarkan sama ...
    public function show($id)
    {
        $meeting = Meeting::with(['schedule.subject', 'schedule.classroom', 'attendances.student', 'opener'])
                          ->findOrFail($id);
        
        $user = Auth::user();
        
        // Logika Keamanan:
        // Boleh lihat jika: Dia Guru Asli OR Dia Admin OR Dia yang Buka Kelas (Piket)
        if ($meeting->schedule->teacher_id != $user->id && $user->role != 'admin' && $meeting->opened_by != $user->id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ini.');
        }

        return view('teacher.meeting_show', compact('meeting'));
    }

    public function toggleStatus($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->is_active = !$meeting->is_active;
        $meeting->save();
        return back();
    }

        public function regenerateQr($id)
    {
        $meeting = Meeting::findOrFail($id);

        // Validasi Keamanan (Hanya Guru ybs/Admin/Piket yg boleh ubah)
        $user = Auth::user();
        if ($meeting->schedule->teacher_id != $user->id && $user->role != 'admin' && $meeting->opened_by != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // 1. Ganti Token dengan yang baru
        $newToken = Str::random(40);
        $meeting->update(['qr_token' => $newToken]);

        // 2. Render ulang QR Codenya
        // Kita kirim balik HTML SVG dari QR Code tersebut
        $qrCodeHtml = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(280)->margin(1)->generate($newToken);

        return response()->json([
            'status' => 'success',
            'qr_html' => (string) $qrCodeHtml
        ]);
    }
}