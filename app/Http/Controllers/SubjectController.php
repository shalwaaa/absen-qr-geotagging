<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
        {
            $grade = $request->query('grade'); // Ambil filter tingkat
            $search = $request->query('search');

            $query = \App\Models\Subject::query();

            // Filter Tingkat
            if ($grade) {
                $query->where('grade_level', $grade);
            }

            // Filter Search
            if ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            $subjects = $query->orderBy('grade_level', 'asc')
                            ->orderBy('name', 'asc')
                            ->paginate(2)
                            ->withQueryString();

            return view('admin.subjects.index', compact('subjects', 'grade', 'search'));
        }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code',
        ]);

        Subject::create($request->all());

        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code,'.$subject->id,
        ]);

        $subject->update($request->all());

        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil diperbarui!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}