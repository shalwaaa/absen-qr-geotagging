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
        $query = Classroom::query();

        if ($request->filled('grade')) {
            $query->where('grade_level', $request->grade);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $classrooms = $query->paginate(10);

        return view('admin.schedules.index', [
            'classrooms' => $classrooms,
            'grade' => $request->grade,
            'search' => $request->search,
        ]);
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