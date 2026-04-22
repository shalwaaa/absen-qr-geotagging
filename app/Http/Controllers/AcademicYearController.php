<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
// Tambahkan dua import ini untuk Laravel 11
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

// Tambahkan "implements HasMiddleware"
class AcademicYearController extends Controller implements HasMiddleware
{
    /**
     * Tentukan middleware untuk controller ini (Cara Laravel 11)
     */
    public static function middleware(): array
    {
        return[
            new Middleware(function ($request, $next) {
                if (auth()->user()->role !== 'admin') {
                    abort(403, 'Akses Ditolak. Halaman ini khusus Admin.');
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        // Ambil semua tahun, urutkan dari yang terbaru
        $years = AcademicYear::orderBy('name', 'desc')->paginate(10);
        return view('admin.academic_years.index', compact('years'));
    }

    public function setActive($id)
    {
        // 1. Nonaktifkan SEMUA tahun ajaran
        AcademicYear::query()->update(['is_active' => false]);

        // 2. Aktifkan HANYA tahun yang dipilih
        $year = AcademicYear::findOrFail($id);
        $year->update(['is_active' => true]);

        return back()->with('success', 'Tahun Ajaran ' . $year->name . ' sekarang AKTIF.');
    }
}