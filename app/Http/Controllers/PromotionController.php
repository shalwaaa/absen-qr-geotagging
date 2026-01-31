<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Models\ClassMember; 
use App\Models\AcademicYear; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::orderBy('grade_level', 'asc')
                               ->orderBy('name', 'asc')
                               ->get();
        return view('admin.promotions.index', compact('classrooms'));
    }

    public function getStudents($classroomId)
    {
        $students = User::where('classroom_id', $classroomId)
                        ->where('role', 'student')
                        ->where('status', 'active') 
                        ->orderBy('name')
                        ->get(['id', 'name', 'nip_nis']); 
        
        return response()->json($students);
    }

    public function process(Request $request)
    {
        $request->validate([
            'from_classroom_id' => 'required',
            'student_ids' => 'required|array', 
            'action' => 'required', 
            'to_classroom_id' => 'required_if:action,promote',
        ]);

        // 1. Ambil Tahun Ajaran yang sedang AKTIF
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Validasi: Admin harus set tahun ajar dulu sebelum naik kelas
        if (!$activeYear) {
            return back()->with('error', 'Belum ada Tahun Ajaran Aktif! Silakan setting di menu Tahun Ajar.');
        }

        DB::transaction(function () use ($request, $activeYear) {
            
            // Kita loop per siswa agar history-nya tercatat rapi
            foreach ($request->student_ids as $studentId) {

                // A. Non-aktifkan status Rombel lama (History)
                ClassMember::where('student_id', $studentId)
                           ->where('is_active', true)
                           ->update(['is_active' => false]);

                // B. Cek Aksi
                if ($request->action == 'promote') {
                    // 1. Update data Master User (Pindah Kelas)
                    User::where('id', $studentId)->update([
                        'classroom_id' => $request->to_classroom_id
                    ]);

                    // 2. Buat Data Anggota Rombel BARU (History Baru)
                    ClassMember::create([
                        'student_id' => $studentId,
                        'classroom_id' => $request->to_classroom_id,
                        'academic_year_id' => $activeYear->id, // Pake ID tahun ajar aktif
                        'is_active' => true,
                        'attendance_percentage' => 0
                    ]);
                } 
                
                elseif ($request->action == 'graduate') {
                    // 1. Update data Master User (Jadi Alumni/Lulus)
                    User::where('id', $studentId)->update([
                        'classroom_id' => null, // Tidak punya kelas lagi
                        'status' => 'graduated'
                    ]);
                    
                }
            }
        });

        return back()->with('success', count($request->student_ids) . ' Siswa berhasil diproses!');
    }
}