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
        $grade = $request->query('grade');
        $day = $request->query('day'); // <-- Filter Hari
        $search = $request->query('search');

        $query = \App\Models\Schedule::with(['teacher', 'subject', 'classroom']);

        // Filter Tingkat Kelas
        if ($grade) {
            $query->whereHas('classroom', function($q) use ($grade) {
                $q->where('grade_level', $grade);
            });
        }

        // Filter Hari (BARU)
        if ($day) {
            $query->where('day', $day);
        }

        // Filter Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('teacher', fn($t) => $t->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('classroom', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $schedules = $query->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
                           ->orderBy('start_time')
                           ->paginate(10)
                           ->withQueryString();

        return view('admin.schedules.index', compact('schedules', 'grade', 'day', 'search'));
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
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time', 
        ]);

        Schedule::create($request->all());

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dibuat!');
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
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $schedule->update($request->all());

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Jadwal dihapus!');
    }
}