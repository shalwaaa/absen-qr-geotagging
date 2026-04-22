<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 1. Tampilkan Halaman Scan
    public function index()
    {
        return view('student.scan');
    }

    // 2. Proses Data Scan
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'qr_token' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // Cek Status Siswa (Active/Lulus/Keluar)
        if (Auth::user()->status != 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Status akun Anda tidak aktif / sudah lulus.'
            ], 403);
        }

        try {
            // Cari Meeting berdasarkan Token
            $meeting = Meeting::where('qr_token', $request->qr_token)
                              ->with('schedule.classroom')
                              ->first();

            // Validasi Dasar Meeting
            if (!$meeting) return response()->json(['status' => 'error', 'message' => 'QR Code Salah/Tidak Ditemukan.'], 404);
            if (!$meeting->is_active) return response()->json(['status' => 'error', 'message' => 'Sesi absensi sudah ditutup Guru.'], 400);
            if (!$meeting->schedule || !$meeting->schedule->classroom) return response()->json(['status' => 'error', 'message' => 'Data Jadwal/Kelas tidak lengkap.'], 500);

            // VALIDASI KELAS SISWA (Anti Salah Masuk Kelas)
            $studentClassId = Auth::user()->classroom_id;
            $meetingClassId = $meeting->schedule->classroom_id;

            if (!$studentClassId) {
                return response()->json(['status' => 'error', 'message' => 'Anda belum terdaftar di kelas manapun. Hubungi Admin.'], 403);
            }

            if ($studentClassId != $meetingClassId) {
                $studentClassName = Auth::user()->classroom->name ?? '-';
                return response()->json(['status' => 'error', 'message' => "Anda siswa kelas $studentClassName. Tidak bisa absen di kelas ini."], 403);
            }

            // Validasi Double Absen
            $alreadyPresent = Attendance::where('meeting_id', $meeting->id)
                                        ->where('student_id', Auth::id())
                                        ->exists();
            if ($alreadyPresent) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen di sesi ini!'], 400);
            }

            // VALIDASI JARAK (DOUBLE LOCATION)
            $classroom = $meeting->schedule->classroom;
            $radiusAllowed = $classroom->radius_meters;

            $distance1 = $this->calculateDistance($request->latitude, $request->longitude, $classroom->latitude, $classroom->longitude);
            $distance2 = $classroom->latitude2 ? $this->calculateDistance($request->latitude, $request->longitude, $classroom->latitude2, $classroom->longitude2) : 999999;
            $minDistance = min($distance1, $distance2);

            Log::info("Absen Siswa: " . Auth::user()->name . " | Jarak Terdekat: $minDistance m");

            if ($minDistance > $radiusAllowed) {
                return response()->json(['status' => 'error', 'message' => 'Jarak terlalu jauh! Anda berjarak ' . round($minDistance) . 'm. (Maks: ' . $radiusAllowed . 'm)'], 400);
            }

            // ========================================================
            // LOGIKA ABSEN TELAT DENGAN TOKEN BEBAS TELAT
            // ========================================================
            $now = Carbon::now('Asia/Jakarta');
            $scheduleStart = Carbon::parse($meeting->schedule->start_time, 'Asia/Jakarta');
            
            // Toleransi telat 15 menit
            $lateThreshold = $scheduleStart->copy()->addMinutes(15);
            
            $status = 'present'; // Default: Hadir
            $usedToken = null;

            // JIKA WAKTU SCAN MELEBIHI BATAS TOLERANSI
            if ($now->greaterThan($lateThreshold)) {
                $status = 'late'; // Ubah status jadi telat
                
                // CEK TAS SISWA: Punya Token Bebas Telat nggak?
                $token = \App\Models\UserToken::where('user_id', Auth::id())
                            ->where('status', 'AVAILABLE')
                            ->whereHas('item', function($q) {
                                $q->where('item_type', 'late_pass'); 
                            })->first();

                if ($token) {
                    $status = 'present'; // DIMAAFKAN! Status balik jadi Hadir
                    $usedToken = $token; // Tandai token ini untuk dihanguskan
                }
            }

            // ========================================================
            // SIMPAN DATA ABSENSI (HANYA SEKALI DISINI)
            // ========================================================
            $attendance = Attendance::create([
                'meeting_id' => $meeting->id,
                'student_id' => Auth::id(),
                'status' => $status, // Status dinamis (present / late)
                'latitude_student' => $request->latitude,
                'longitude_student' => $request->longitude,
                'distance_meters' => $minDistance,
                'scan_time' => $now,
            ]);

            // HANGUSKAN TOKEN JIKA DIPAKAI
            $pesanTambahan = '';
            if ($usedToken) {
                $usedToken->update([
                    'status' => 'USED',
                    'used_at_attendance_id' => $attendance->id
                ]);
                $pesanTambahan = '<br><small>(Menggunakan Token Bebas Telat!)</small>';
            }

            // Berikan balasan sukses ke HP Siswa
            $teksStatus = $status == 'late' ? 'TERLAMBAT' : 'TEPAT WAKTU';
            return response()->json([
                'status' => 'success',
                'message' => "Hadir ($teksStatus) dengan jarak " . round($minDistance) . "m. $pesanTambahan"
            ]);

        } catch (\Exception $e) {
            Log::error("Error Absensi: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Rumus Haversine
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; 
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c; 
    }
}