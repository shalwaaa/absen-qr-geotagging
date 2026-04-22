<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        // Fitur Pencarian
        $search = $request->input('search');
        $query = Classroom::with('homeroomTeacher');

        // Pencarian berdasarkan nama kelas atau nama wali kelas
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('homeroomTeacher', function($teacherQuery) use ($search) {
                      $teacherQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Urutkan berdasarkan tingkat kelas (grade_level) dan nama kelas
        $classrooms = $query->orderBy('grade_level', 'asc')
                            ->orderBy('name', 'asc')
                            ->paginate(10)
                            ->withQueryString();

        return view('admin.classrooms.index', compact('classrooms', 'search'));
    }

    public function create()
    {
        // Ambil daftar guru untuk dropdown wali kelas
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classrooms.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        //dd($request->all());

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'radius_meters' => 'required|integer|min:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'latitude2' => 'nullable|numeric',
            'longitude2' => 'nullable|numeric',
        ]);

        Classroom::create($request->all());

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function edit(Classroom $classroom)
    {
        // Ambil daftar guru untuk dropdown wali kelas
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classrooms.edit', compact('classroom', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'radius_meters' => 'required|integer|min:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'latitude2' => 'nullable|numeric',
            'longitude2' => 'nullable|numeric',
        ]);

        $classroom->update($request->all());

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil diperbarui!');
    }

    public function destroy(Classroom $classroom)
    {
        // Hapus data kelas beserta relasinya
        $classroom->delete();
        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus!');
    }

    public function show(Classroom $classroom)
    {
        // Tampilkan detail kelas beserta wali kelas
        return view('admin.classrooms.show', compact('classroom'));
    }

    public function destroyAll()
    {
        // Hapus semua data kelas beserta relasinya dalam satu transaksi untuk menjaga integritas data
        DB::transaction(function () {
            // Ambil semua ID kelas yang akan dihapus
            $classroomIds = Classroom::pluck('id');

            if ($classroomIds->isEmpty()) {
                return; // Tidak ada data, langsung keluar
            }

            // Hapus data di tabel class_members yang mengacu ke kelas
            DB::table('class_members')->whereIn('classroom_id', $classroomIds)->delete();

            // Hapus data di tabel schedules yang mengacu ke kelas
            DB::table('schedules')->whereIn('classroom_id', $classroomIds)->delete();

            // Hapus semua kelas
            Classroom::whereIn('id', $classroomIds)->delete();
        });

        return redirect()->route('classrooms.index')
                         ->with('success', 'Semua data kelas beserta relasinya berhasil dihapus.');
    }
}
