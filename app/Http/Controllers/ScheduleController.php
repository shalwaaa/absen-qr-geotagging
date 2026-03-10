<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Filter
        $grade = $request->query('grade');
        $search = $request->query('search');

        // 2. Query ke CLASSROOM (Bukan Schedule)
        // Karena kita mau menampilkan daftar kelas dulu (Folder Style)
        $query = \App\Models\Classroom::query();

        // Filter Tingkat Kelas
        if ($grade) {
            $query->where('grade_level', $grade);
        }

        // Filter Pencarian Nama Kelas
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // 3. Eksekusi dengan Pagination (PENTING: Pakai paginate, jangan get)
        $classrooms = $query->orderBy('grade_level', 'asc')
                            ->orderBy('name', 'asc')
                            ->paginate(10) // <--- INI KUNCINYA AGAR ERROR HILANG
                            ->withQueryString();

        return view('admin.schedules.index', compact('classrooms', 'grade', 'search'));
    }

    public function classroomShow(Classroom $classroom, Request $request)
    {
        $query = $classroom->schedules()->with(['subject', 'teacher']);

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        $schedules = $query->get(); // atau paginate jika perlu

        return view('admin.schedules.show', [
            'classroom' => $classroom,
            'schedules' => $schedules,
            'day' => $request->day,
        ]);
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        $subjects = Subject::all();
        $classrooms = Classroom::all();

        return view('admin.schedules.create', compact('teachers', 'subjects', 'classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            
            // TAMBAHAN VALIDASI
            'week_type' => 'required|in:all,odd,even', 
        ]);

        // Simpan semua data request (termasuk week_type)
        Schedule::create($request->all());

        // Jika kamu ingin redirect kembali ke halaman detail kelas (agar user tidak bingung)
        return redirect()->route('schedules.classroom.show', $request->classroom_id)
                         ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit(Schedule $schedule)
    {
        $teachers = User::where('role', 'teacher')->get();
        $subjects = Subject::all();
        $classrooms = Classroom::all();
        
        return view('admin.schedules.edit', compact('schedule', 'teachers', 'subjects', 'classrooms'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            
            // TAMBAHAN VALIDASI
            'week_type' => 'required|in:all,odd,even',
        ]);

        $schedule->update($request->all());

        // Redirect kembali ke halaman detail kelas
        return redirect()->route('schedules.classroom.show', $schedule->classroom_id)
                         ->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Jadwal dihapus!');
    }
}