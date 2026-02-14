<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Halaman Filter
    public function index()
    {
        $classrooms = Classroom::all();
        return view('admin.reports.index', compact('classrooms'));
    }

    // Proses Generate PDF
    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:student,teacher', // Tambah validasi tipe
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            // Classroom ID hanya wajib jika tipe-nya student
            'classroom_id' => 'required_if:type,student',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // --- LOGIKA LAPORAN GURU ---
        if ($request->type == 'teacher') {
            return $this->generateTeacherReport($startDate, $endDate);
        }

        // --- LOGIKA LAPORAN SISWA (Kode Lama) ---
        return $this->generateStudentReport($request->classroom_id, $startDate, $endDate);
    }

    // Fungsi Private untuk Siswa (Dipisah biar rapi)
    private function generateStudentReport($classroomId, $startDate, $endDate)
    {
        $classroom = Classroom::findOrFail($classroomId);
        $students = User::where('classroom_id', $classroom->id)
                        ->where('role', 'student')
                        ->orderBy('name')
                        ->get();

        $data = [];

        foreach ($students as $student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->whereHas('meeting', function($q) use ($startDate, $endDate, $classroom) {
                    $q->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->whereHas('schedule', function($sq) use ($classroom) {
                          $sq->where('classroom_id', $classroom->id);
                      });
                })->get();

            $present = $attendances->where('status', 'present')->count();
            $sick = $attendances->where('status', 'sick')->count();
            $perm = $attendances->where('status', 'permission')->count();
            $total = $present + $sick + $perm;

            $data[] = [
                'name' => $student->name,
                'nis' => $student->nip_nis,
                'present' => $present,
                'sick' => $sick,
                'perm' => $perm,
                'total' => $total,
                'percent' => $total > 0 ? round(($present / $total) * 100) . '%' : '0%'
            ];
        }

        $pdf = Pdf::loadView('admin.reports.pdf_student', compact('classroom', 'startDate', 'endDate', 'data'));
        return $pdf->stream('Laporan-Siswa-'.$classroom->name.'.pdf');
    }

    // Fungsi Private untuk Guru (BARU)
    // Fungsi Private untuk Guru (VERSI LENGKAP S/I/A)
    private function generateTeacherReport($startDate, $endDate)
    {
        // 1. Ambil semua guru
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $data = [];

        // 2. Loop setiap guru
        foreach ($teachers as $teacher) {
            
            $stats = [
                'present' => 0,
                'sick' => 0,
                'permission' => 0,
                'alpha' => 0,
                'total_scheduled' => 0
            ];

            // Ambil semua jadwal guru ini (Senin, Selasa, dll)
            $schedules = \App\Models\Schedule::where('teacher_id', $teacher->id)->get();

            // 3. Loop setiap HARI dalam rentang tanggal yang dipilih
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $dayName = $date->locale('id')->isoFormat('dddd'); // Senin, Selasa...
                
                // Cek berapa sesi guru ini mengajar di hari tersebut
                $dailySchedulesCount = $schedules->where('day', $dayName)->count();

                if ($dailySchedulesCount > 0) {
                    $stats['total_scheduled'] += $dailySchedulesCount;

                    // A. Cek Izin/Sakit di Tanggal Ini
                    $leave = \App\Models\LeaveRequest::where('student_id', $teacher->id) // Reuse kolom student_id sbg user_id
                        ->where('status', 'approved')
                        ->whereDate('start_date', '<=', $date)
                        ->whereDate('end_date', '>=', $date)
                        ->first();

                    if ($leave) {
                        if ($leave->type == 'sick') {
                            $stats['sick'] += $dailySchedulesCount;
                        } else {
                            $stats['permission'] += $dailySchedulesCount;
                        }
                        continue; // Lanjut hari berikutnya (karena sudah izin)
                    }

                    // B. Cek Kehadiran (Scan QR)
                    // Cari meeting di tanggal ini dimana guru ini hadir
                    $presents = Attendance::where('student_id', $teacher->id)
                        ->where('status', 'present')
                        ->whereHas('meeting', function($q) use ($date) {
                            $q->whereDate('date', $date);
                        })
                        ->count();

                    $stats['present'] += $presents;

                    // C. Hitung Alpha (Jadwal - (Hadir + Izin))
                    // Jika dia jadwal 3 kelas, tapi cuma scan 1, berarti 2 Alpha
                    // Jika Izin, Alpha 0.
                    $missing = $dailySchedulesCount - $presents;
                    if ($missing > 0) {
                        $stats['alpha'] += $missing;
                    }
                }
            }

            // Hitung Persentase Kehadiran
            $percentage = $stats['total_scheduled'] > 0 
                ? round(($stats['present'] / $stats['total_scheduled']) * 100) 
                : 0;

            $data[] = [
                'name' => $teacher->name,
                'nip' => $teacher->nip_nis,
                'stats' => $stats,
                'percent' => $percentage
            ];
        }

        $pdf = Pdf::loadView('admin.reports.pdf_teacher', compact('startDate', 'endDate', 'data'));
        // Set orientasi Landscape agar muat banyak kolom
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Laporan-Guru.pdf');
    }
}