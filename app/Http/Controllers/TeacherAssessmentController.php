<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherAssessmentController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();
        
        if (!$classroom) {
            return view('teacher.homeroom.no_class');
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'Tahun ajaran aktif belum diatur Admin.');
        }

        Carbon::setLocale('id');
        $periodMonth = Carbon::now('Asia/Jakarta')->format('m-Y'); 
        $monthName = Carbon::now('Asia/Jakarta')->translatedFormat('F Y');

        // Panggil Kategori BESERTA Pertanyaannya
        $categories = AssessmentCategory::with('questions')->where('is_active', true)->get();

        $students = User::where('classroom_id', $classroom->id)
                        ->where('role', 'student')
                        ->where('status', 'active')
                        ->get();

        $assessments = Assessment::where('evaluator_id', $teacher->id)
                                 ->where('academic_year_id', $activeYear->id)
                                 ->where('period_month', $periodMonth)
                                 ->with('details') 
                                 ->get();

        $assessmentsByKey = $assessments->keyBy('evaluatee_id');

        // **PERUBAHAN: Urutkan siswa (yang sudah dinilai ke bawah, yang belum ke atas)**
        $students = $students->sortBy(function ($student) use ($assessmentsByKey) {
            // Jika sudah dinilai (0), jika belum (1), sehingga yang belum ada di atas
            return isset($assessmentsByKey[$student->id]) ? 1 : 0;
        })->values();

        $totalStudents = $students->count();
        $gradedCount = $assessmentsByKey->count();
        $progress = $totalStudents > 0 ? round(($gradedCount / $totalStudents) * 100) : 0;

        // **PERUBAHAN: Hitung data untuk chart rata-rata kelas**
        $chartData = [];
        $chartLabels = [];
        
        if ($assessments->isNotEmpty()) {
            foreach ($categories as $category) {
                if ($category->questions->isNotEmpty()) {
                    foreach ($category->questions as $question) {
                        $totalScore = 0;
                        $count = 0;
                        
                        foreach ($assessments as $assessment) {
                            $detail = $assessment->details->firstWhere('question_id', $question->id);
                            if ($detail) {
                                $totalScore += $detail->score;
                                $count++;
                            }
                        }
                        
                        if ($count > 0) {
                            $chartLabels[] = $question->question; // Nama pertanyaan
                            $chartData[] = round($totalScore / $count, 2);
                        }
                    }
                }
            }
        }

        return view('teacher.assessments.index', compact(
            'classroom', 'students', 'categories', 'assessmentsByKey', 
            'monthName', 'periodMonth', 'activeYear', 
            'totalStudents', 'gradedCount', 'progress',
            'chartData', 'chartLabels' // **PERUBAHAN: Kirim data chart ke view**
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'evaluatee_id' => 'required|exists:users,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'period_month' => 'required',
            'scores' => 'required|array', // Sekarang isinya ID pertanyaan
            'general_notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $assessment = Assessment::updateOrCreate([
                    'evaluator_id' => Auth::id(),
                    'evaluatee_id' => $request->evaluatee_id,
                    'academic_year_id' => $request->academic_year_id,
                    'period_month' => $request->period_month,
                ],[
                    'assessment_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d'),
                    'general_notes' => $request->general_notes
                ]
            );

            // Simpan skor berdasarkan QUESTION_ID
            foreach ($request->scores as $questionId => $score) {
                AssessmentDetail::updateOrCreate([
                        'assessment_id' => $assessment->id,
                        'question_id' => $questionId
                    ],[
                        'score' => $score
                    ]
                );
            }
        });

        return back()->with('success', 'Penilaian berhasil disimpan!');
    }

    // FITUR BARU: MENGHAPUS PENILAIAN
    public function destroy($id)
    {
        $assessment = Assessment::where('id', $id)
                                ->where('evaluator_id', Auth::id()) // Pastikan milik guru ini
                                ->firstOrFail();
        
        $assessment->delete(); // Karena onDelete('cascade'), details-nya juga ikut terhapus

        return back()->with('success', 'Data penilaian berhasil dihapus / direset.');
    }

    // Fungsi Cetak Laporan Karakter Kelas
    public function printReport(Request $request)
    {
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();
        
        $periodMonth = $request->query('period', Carbon::now('Asia/Jakarta')->format('m-Y'));
        
        // Ambil semua penilaian di bulan tsb
        $assessments = Assessment::where('evaluator_id', $teacher->id)
            ->where('academic_year_id', $activeYear->id)
            ->where('period_month', $periodMonth)
            ->with(['evaluatee', 'details.question.category'])
            ->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Belum ada data penilaian untuk dicetak pada periode ini.');
        }

        // Ambil kategori untuk header tabel PDF
        $categories = AssessmentCategory::where('is_active', true)->get();

        $pdf = Pdf::loadView('teacher.assessments.pdf', compact('classroom', 'assessments', 'categories', 'periodMonth', 'activeYear', 'teacher'));
        
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Rapor-Karakter-'.$classroom->name.'.pdf');
    }
}