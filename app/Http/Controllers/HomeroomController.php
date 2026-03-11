<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\Meeting; 
use App\Models\Schedule; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeroomController extends Controller
{
    // 1. Tampilkan Halaman Pengajuan Izin Siswa
    public function index()
    {
        $classroom = Classroom::where('homeroom_teacher_id', Auth::id())->first();

        // Jika tidak ada kelas yang diasuh, tampilkan halaman khusus
        if (!$classroom) {
            return view('teacher.homeroom.no_class');
        }

        $pendingLeaves = LeaveRequest::where('classroom_id', $classroom->id)
                                     ->where('status', 'pending')
                                     ->with('student')
                                     ->orderBy('created_at', 'asc')
                                     ->get();

        $historyLeaves = LeaveRequest::where('classroom_id', $classroom->id)
                                     ->whereIn('status', ['approved', 'rejected'])
                                     ->with('student')
                                     ->orderBy('updated_at', 'desc')
                                     ->paginate(5);

        $students = \App\Models\User::where('classroom_id', $classroom->id)
                                    ->where('role', 'student')
                                    ->orderBy('name', 'asc')
                                    ->get();

        return view('teacher.homeroom.index', compact('classroom', 'pendingLeaves', 'historyLeaves', 'students'));
    }

    // 2. Proses Persetujuan / Penolakan Pengajuan Izin Siswa
    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $leave = LeaveRequest::findOrFail($id);
        
        if ($request->action == 'approve') {
            $leave->status = 'approved';
            
            Carbon::setLocale('id'); 
            
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            // Gunakan variabel sementara agar tanggal asli tidak berubah
            $loopDate = $startDate->copy();

            // Loop while lebih aman daripada for
            while ($loopDate->lte($endDate)) {
                
                $dayName = $loopDate->isoFormat('dddd'); 

                // Cari Jadwal Pelajaran di hari tersebut untuk kelas ini
                $schedules = Schedule::where('classroom_id', $leave->classroom_id)
                                     ->where('day', $dayName) 
                                     ->paginate(10)
                                     ->withQueryString();


                foreach ($schedules as $schedule) {
                    
                    // 1. Pastikan Meeting ada (Buat kalau belum ada)
                    $meeting = Meeting::firstOrCreate(
                        [
                            'schedule_id' => $schedule->id,
                            'date' => $loopDate->format('Y-m-d')
                        ],
                        [
                            'qr_token' => \Illuminate\Support\Str::random(40),
                            'is_active' => false, 
                            'opened_by' => null 
                        ]
                    );

                    // 2. Masukkan Data Izin ke Attendance
                    Attendance::updateOrCreate(
                        [
                            'meeting_id' => $meeting->id,
                            'student_id' => $leave->student_id
                        ],
                        [
                            'status' => $leave->type, // 'sick' atau 'permission'
                            'scan_time' => now(),
                            'distance_meters' => 0, // Wajib 0 atau null
                            'latitude_student' => null,
                            'longitude_student' => null
                        ]
                    );
                }
                
                $loopDate->addDay(); // Lanjut ke besok
            }

        } else {
            $leave->status = 'rejected';
        }

        $leave->notes = $request->notes;
        $leave->save();

        return back()->with('success', 'Status diperbarui. Data absensi otomatis terisi.');
    }
}