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
   // Fungsi Private untuk Laporan Guru
    // Fungsi Private untuk Laporan Guru (VERSI SUPER CEPAT / OPTIMIZED)
    private function generateTeacherReport($startDate, $endDate)
    {
        // Set limit waktu jaga-jaga
        set_time_limit(300); 

        // 1. Ambil semua guru BESERTA jadwalnya sekaligus (Eager Loading)
        $teachers = User::where('role', 'teacher')
                        ->with('schedules') 
                        ->orderBy('name')
                        ->get();

        // 2. Tarik SEMUA Hari Libur di rentang waktu tersebut ke dalam Array
        $holidayDates = \App\Models\Holiday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                           ->pluck('date')
                                           ->map(fn($date) => $date->format('Y-m-d'))
                                           ->toArray();

        // 3. Tarik SEMUA Izin Guru di rentang waktu tersebut, kelompokkan berdasarkan ID Guru
        $leaveRequests = \App\Models\LeaveRequest::whereHas('student', fn($q) => $q->where('role', 'teacher'))
                                                 ->where('status', 'approved')
                                                 ->where(function($q) use ($startDate, $endDate) {
                                                     $q->whereBetween('start_date', [$startDate, $endDate])
                                                       ->orWhereBetween('end_date',[$startDate, $endDate]);
                                                 })
                                                 ->get()
                                                 ->groupBy('student_id');

        // 4. Tarik SEMUA Absen Guru di rentang waktu tersebut, kelompokkan berdasarkan ID Guru
        $attendances = Attendance::whereHas('student', fn($q) => $q->where('role', 'teacher'))
                                 ->where('status', 'present')
                                 ->whereBetween('scan_time', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                                 ->get()
                                 ->groupBy('student_id');

        // 5. Siapkan Hari Valid (Singkirkan Sabtu, Minggu & Libur Nasional di awal biar cepat)
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $validDays =[];
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            
            // Jika Sabtu, Minggu, atau ada di array hari libur -> Skip
            if ($date->isWeekend() || in_array($dateString, $holidayDates)) {
                continue;
            }
            // Simpan nama hari (Senin, Selasa...)
            $validDays[$dateString] = $date->locale('id')->isoFormat('dddd');
        }

        $data =[];

        // --- MULAI PERHITUNGAN (Semuanya terjadi di RAM, sangat cepat) ---
        foreach ($teachers as $teacher) {
            
            $present = 0;
            $sick = 0;
            $permission = 0;
            $alpha = 0;
            $totalScheduled = 0;

            // Ambil data izin dan absen khusus guru ini dari memori
            $teacherLeaves = $leaveRequests->get($teacher->id, collect());
            $teacherAttendances = $attendances->get($teacher->id, collect());

            // Loop HANYA di hari-hari kerja
            foreach ($validDays as $dateString => $dayName) {
                
                // Berapa jadwal guru ini di hari tersebut?
                $dailySchedulesCount = $teacher->schedules->where('day', $dayName)->count();

                if ($dailySchedulesCount > 0) {
                    $totalScheduled += $dailySchedulesCount;

                    // A. Cek Izin/Sakit
                    $isLeave = false;
                    foreach ($teacherLeaves as $leave) {
                        $startLeave = \Carbon\Carbon::parse($leave->start_date)->format('Y-m-d');
                        $endLeave = \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d');
                        
                        if ($dateString >= $startLeave && $dateString <= $endLeave) {
                            if ($leave->type == 'sick') {
                                $sick += $dailySchedulesCount;
                            } else {
                                $permission += $dailySchedulesCount;
                            }
                            $isLeave = true;
                            break;
                        }
                    }

                    if ($isLeave) continue; // Lanjut ke hari berikutnya

                    // B. Cek Kehadiran
                    $presentsCount = $teacherAttendances->filter(function($att) use ($dateString) {
                        return \Carbon\Carbon::parse($att->scan_time)->format('Y-m-d') === $dateString;
                    })->count();

                    $present += $presentsCount;

                    // C. Hitung Alpha
                    $missing = $dailySchedulesCount - $presentsCount;
                    if ($missing > 0) {
                        $alpha += $missing;
                    }
                }
            }

            // Hitung Persentase Akhir
            $percentage = $totalScheduled > 0 ? round(($present / $totalScheduled) * 100) : 0;

            $data[] =[
                'name' => $teacher->name,
                'nip' => $teacher->nip_nis,
                'present' => $present,
                'sick' => $sick,
                'permission' => $permission,
                'alpha' => $alpha,
                'total_scheduled' => $totalScheduled,
                'percent' => $percentage
            ];
        }

        // Render PDF
        $pdf = Pdf::loadView('admin.reports.pdf_teacher', compact('startDate', 'endDate', 'data'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Laporan-Aktivitas-Guru.pdf');
    }
}