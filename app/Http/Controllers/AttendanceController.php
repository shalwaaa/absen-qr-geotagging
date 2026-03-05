<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // CEK 1: Status Siswa (Active/Lulus/Keluar)
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
            if (!$meeting) {
                return response()->json(['status' => 'error', 'message' => 'QR Code Salah/Tidak Ditemukan.'], 404);
            }

            if (!$meeting->is_active) {
                return response()->json(['status' => 'error', 'message' => 'Sesi absensi sudah ditutup Guru.'], 400);
            }

            if (!$meeting->schedule || !$meeting->schedule->classroom) {
                return response()->json(['status' => 'error', 'message' => 'Data Jadwal/Kelas tidak lengkap.'], 500);
            }

            // ========================================================
            // VALIDASI KELAS SISWA (Anti Salah Masuk Kelas)
            // ========================================================
            $studentClassId = Auth::user()->classroom_id;
            $meetingClassId = $meeting->schedule->classroom_id;

            if (!$studentClassId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda belum terdaftar di kelas manapun. Hubungi Admin.'
                ], 403);
            }

            if ($studentClassId != $meetingClassId) {
                $studentClassName = Auth::user()->classroom->name ?? '-';
                return response()->json([
                    'status' => 'error',
                    'message' => "Anda siswa kelas $studentClassName. Tidak bisa absen di kelas ini."
                ], 403);
            }

            // Validasi Double Absen
            $alreadyPresent = Attendance::where('meeting_id', $meeting->id)
                                        ->where('student_id', Auth::id())
                                        ->exists();
            if ($alreadyPresent) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen di sesi ini!'], 400);
            }

            // ========================================================
            // VALIDASI JARAK (DOUBLE LOCATION)
            // ========================================================
            $classroom = $meeting->schedule->classroom;
            $radiusAllowed = $classroom->radius_meters;

            // Hitung Jarak 1
            $distance1 = $this->calculateDistance(
                $request->latitude, 
                $request->longitude, 
                $classroom->latitude, 
                $classroom->longitude
            );

            // Hitung Jarak 2 (Jika ada)
            $distance2 = $classroom->latitude2 ? $this->calculateDistance(
                $request->latitude, 
                $request->longitude, 
                $classroom->latitude2, 
                $classroom->longitude2
            ) : 999999; // Angka besar jika tidak ada lokasi 2

            // Ambil jarak terdekat
            $minDistance = min($distance1, $distance2);

            Log::info("Absen Siswa: " . Auth::user()->name . " | Jarak 1: $distance1 | Jarak 2: $distance2 | Final: $minDistance");

            if ($minDistance > $radiusAllowed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jarak terlalu jauh! Anda berjarak ' . round($minDistance) . 'm dari lokasi terdekat. (Maks: ' . $radiusAllowed . 'm)'
                ], 400);
            }

            // SIMPAN DATA
            Attendance::create([
                'meeting_id' => $meeting->id,
                'student_id' => Auth::id(),
                'status' => 'present',
                'latitude_student' => $request->latitude,
                'longitude_student' => $request->longitude,
                'distance_meters' => $minDistance, // <--- PERBAIKAN: Gunakan $minDistance
                'scan_time' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil! Jarak: ' . round($minDistance) . 'm' // <--- PERBAIKAN: Gunakan $minDistance
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