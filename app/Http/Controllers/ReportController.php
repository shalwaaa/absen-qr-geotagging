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
    // Halaman Filter (Admin memilih kelas & tanggal)
    public function index()
    {
        $classrooms = Classroom::all();
        return view('admin.reports.index', compact('classrooms'));
    }

    // Proses Generate PDF
    public function generate(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $classroom = Classroom::findOrFail($request->classroom_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Ambil Siswa di kelas ini
        $students = User::where('classroom_id', $classroom->id)
                        ->where('role', 'student')
                        ->orderBy('name')
                        ->get();

        $data = [];

        foreach ($students as $student) {
            // Hitung statistik berdasarkan tabel attendance
            // Query: Ambil absen siswa ini, yang meeting-nya ada di rentang tanggal tersebut
            $attendances = Attendance::where('student_id', $student->id)
                ->whereHas('meeting', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })
                ->get();

            $present = $attendances->where('status', 'present')->count();
            $sick = $attendances->where('status', 'sick')->count();
            $perm = $attendances->where('status', 'permission')->count();
            
            // Alpha hitungannya agak kompleks (biasanya total hari efektif - total hadir), 
            // tapi untuk sekarang kita anggap 0 atau hitung manual jika ada fitur hari libur.
            // Kita pakai Total Kehadiran saja.
            $total = $present + $sick + $perm;

            $data[] = [
                'name' => $student->name,
                'nis' => $student->nip_nis,
                'present' => $present,
                'sick' => $sick,
                'perm' => $perm,
                'total' => $total,
                // Persentase (Hadir / Total Pertemuan) * 100
                'percent' => $total > 0 ? round(($present / $total) * 100) . '%' : '0%'
            ];
        }

        // Generate PDF
        $pdf = Pdf::loadView('admin.reports.pdf', compact('classroom', 'startDate', 'endDate', 'data'));
        return $pdf->stream('Laporan-'.$classroom->name.'.pdf');
    }
}