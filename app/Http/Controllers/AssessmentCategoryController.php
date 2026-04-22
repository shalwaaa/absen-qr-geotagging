<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use Illuminate\Http\Request;
use App\Models\AssessmentQuestion;

class AssessmentCategoryController extends Controller
{
    // Fungsi bantuan untuk mengecek hak akses (Hanya Admin / Kepsek)
    private function checkAccess()
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && !$user->is_headmaster) {
            abort(403, 'Akses Ditolak. Halaman ini khusus Admin dan Kepala Sekolah.');
        }
    }

    public function index()
    {
        $this->checkAccess(); // Panggil pengecekan

        // Ambil semua kategori, urutkan dari yang terbaru
        $categories = AssessmentCategory::latest()->paginate(7);
        return view('admin.assessments.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'name' => 'required|string|max:255|unique:assessment_categories,name',
            'description' => 'nullable|string'
        ]);

        AssessmentCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true // Default aktif
        ]);

        return back()->with('success', 'Kategori Penilaian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess();

        $category = AssessmentCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:assessment_categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Kategori Penilaian berhasil diperbarui.');
    }

    // Fungsi Toggle Aktif/Nonaktif (Soft Delete semu)
    public function toggleStatus($id)
    {
        $this->checkAccess();

        $category = AssessmentCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori Penilaian berhasil $status.");
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'category_id' => 'required|exists:assessment_categories,id'
        ]);

        AssessmentQuestion::create([
            'question' => $request->question,
            'category_id' => $request->category_id,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }
}