<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
public function index(Request $request)
    {
        // 1. Ambil kata kunci pencarian
        $search = $request->input('search');

        // 2. Mulai Query dengan Eager Loading Wali Kelas
        $query = Classroom::with('homeroomTeacher');

        // 3. Logika Pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%') // Cari Nama Kelas
                  ->orWhereHas('homeroomTeacher', function($teacherQuery) use ($search) {
                      $teacherQuery->where('name', 'like', '%' . $search . '%'); // Cari Nama Wali Kelas
                  });
            });
        }

        // 4. Logika Pengurutan (Sorting) & Pagination
        $classrooms = $query->orderBy('grade_level', 'asc')
                            ->orderBy('name', 'asc')
                            ->paginate(10)
                            ->withQueryString(); // Agar search tidak hilang saat ganti halaman

        // 5. Kembalikan ke View
        return view('admin.classrooms.index', compact('classrooms', 'search'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classrooms.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required', 
            'longitude' => 'required',
            'radius_meters' => 'required|integer|min:10',
            'grade_level' => 'required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        Classroom::create($request->all());

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function edit(Classroom $classroom)
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classrooms.edit', compact('classroom', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius_meters' => 'required|integer|min:10',
            'grade_level' => 'required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        $classroom->update($request->all());

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil diperbarui!');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus!');
    }

    public function show(Classroom $classroom)
    {
        return view('admin.classrooms.show', compact('classroom'));
    }
}