<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('student.scan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'qr_token' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // 1. CEK STATUS SISWA DULU (Pindahkan ke sini)
        if (Auth::user()->status != 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Status Anda tidak aktif / sudah lulus.'
            ], 403);
        }

        try {
            $meeting = Meeting::where('qr_token', $request->qr_token)
                              ->with('schedule.classroom')
                              ->first();

            if (!$meeting) {
                return response()->json(['status' => 'error', 'message' => 'QR Code Salah/Tidak Ditemukan.'], 404);
            }

            if (!$meeting->is_active) {
                return response()->json(['status' => 'error', 'message' => 'Sesi absensi sudah ditutup Guru.'], 400);
            }

            if (!$meeting->schedule || !$meeting->schedule->classroom) {
                return response()->json(['status' => 'error', 'message' => 'Data Jadwal/Kelas tidak lengkap.'], 500);
            }

            $alreadyPresent = Attendance::where('meeting_id', $meeting->id)
                                        ->where('student_id', Auth::id())
                                        ->exists();
            if ($alreadyPresent) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen di sesi ini!'], 400);
            }

            $schoolLat = $meeting->schedule->classroom->latitude;
            $schoolLng = $meeting->schedule->classroom->longitude;
            $radiusAllowed = $meeting->schedule->classroom->radius_meters;

            $distance = $this->calculateDistance(
                $request->latitude, 
                $request->longitude, 
                $schoolLat, 
                $schoolLng
            );

            Log::info("Absen Siswa: " . Auth::user()->name . " | Jarak: " . $distance . "m");

            if ($distance > $radiusAllowed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jarak terlalu jauh! Anda berjarak ' . round($distance) . 'm. (Maks: ' . $radiusAllowed . 'm)'
                ], 400);
            }

            Attendance::create([
                'meeting_id' => $meeting->id,
                'student_id' => Auth::id(),
                'status' => 'present',
                'latitude_student' => $request->latitude,
                'longitude_student' => $request->longitude,
                'distance_meters' => $distance,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil! Jarak: ' . round($distance) . 'm'
            ]);

        } catch (\Exception $e) {
            Log::error("Error Absensi: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

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