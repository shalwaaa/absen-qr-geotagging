<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.academic_years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50', // Misal: 2025/2026 Ganjil
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Cek apakah ini data pertama? Jika iya, otomatis aktif
        $isActive = AcademicYear::count() === 0 ? true : false;

        AcademicYear::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $isActive
        ]);

        return back()->with('success', 'Tahun ajaran berhasil dibuat!');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $academicYear->update($request->all());

        return back()->with('success', 'Data diperbarui!');
    }

    // Fitur Penting: Set Aktif
    public function setActive($id)
    {
        // 1. Non-aktifkan SEMUA tahun ajaran
        AcademicYear::query()->update(['is_active' => false]);

        // 2. Aktifkan tahun yang dipilih
        $year = AcademicYear::findOrFail($id);
        $year->update(['is_active' => true]);

        return back()->with('success', 'Tahun Ajaran ' . $year->name . ' sekarang AKTIF.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Tidak bisa menghapus tahun ajaran yang sedang aktif!');
        }
        $academicYear->delete();
        return back()->with('success', 'Data dihapus.');
    }
}