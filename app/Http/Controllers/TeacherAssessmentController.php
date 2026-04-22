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
        // 1. Cek apakah guru ini adalah wali kelas dari suatu kelas
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->first();
        if (!$classroom) return view('teacher.homeroom.no_class');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return back()->with('error', 'Tahun ajaran aktif belum diatur Admin.');

        Carbon::setLocale('id');
        $periodMonth = Carbon::now('Asia/Jakarta')->format('m-Y'); 
        $monthName = Carbon::now('Asia/Jakarta')->translatedFormat('F Y');

        // Panggil Kategori beserta pertanyaannya
        $categories = AssessmentCategory::with('questions')->where('is_active', true)->get();

        if ($categories->isEmpty()) {
            return back()->with('error', 'Admin belum membuat Kategori Penilaian.');
        }

        // Panggil semua siswa aktif di kelas ini
        $students = User::where('classroom_id', $classroom->id)
                        ->where('role', 'student')
                        ->where('status', 'active')
                        ->get();

        // Panggil Assessment beserta detail (object)
        $assessments = Assessment::where('evaluator_id', $teacher->id)
                                 ->where('academic_year_id', $activeYear->id)
                                 ->where('period_month', $periodMonth)
                                 ->with('details.question')
                                 ->get();

        $assessmentsByKey = $assessments->keyBy('evaluatee_id');

        // LOGIKA PENGURUTAN PRIORITAS
        // Siswa belum dinilai di atas, sudah dinilai di bawah.
        $students = $students->sort(function ($a, $b) use ($assessmentsByKey) {
            $aAssessed = isset($assessmentsByKey[$a->id]) ? 1 : 0;
            $bAssessed = isset($assessmentsByKey[$b->id]) ? 1 : 0;

            // Jika status penilaian berbeda, taruh yang belum dinilai (0) di atas yang sudah (1)
            if ($aAssessed !== $bAssessed) {
                return $aAssessed <=> $bAssessed;
            }

            // Jika statusnya sama (sama-sama belum atau sama-sama sudah), urutkan berdasarkan nama A-Z
            return strcasecmp($a->name, $b->name);
        })->values();


        // LOGIKA HITUNG RATA-RATA PER KATEGORI (UNTUK CHART)
        $chartLabels = [];
        $chartData =[];
        
        
        //menghitung rata-rata per kategori untuk grafik radar
        if ($assessments->isNotEmpty()) {
            $allDetails = $assessments->pluck('details')->flatten();//menyatukan banyak array
            // Loop setiap kategori, cari pertanyaan di kategori itu, lalu hitung rata-rata skor untuk pertanyaan-pertanyaan itu
            foreach ($categories as $cat) {
                $questionIds = $cat->questions->pluck('id')->toArray();
                $scoresForCat = $allDetails->whereIn('question_id', $questionIds)->pluck('score');
                
                $avg = $scoresForCat->count() > 0 ? round($scoresForCat->avg(), 2) : 0;
                
                $chartLabels[] = $cat->name;
                $chartData[] = $avg;
            }
        }


        // LOGIKA HITUNG PROGRESS PENILAIAN
        $totalStudents = $students->count();
        $gradedCount = $assessmentsByKey->count();          
        $progress = $totalStudents > 0 ? round(($gradedCount / $totalStudents) * 100) : 0;

        return view('teacher.assessments.index', compact(
            'classroom', 'students', 'categories', 'assessmentsByKey', 
            'monthName', 'periodMonth', 'activeYear', 
            'totalStudents', 'gradedCount', 'progress',
            'chartLabels', 'chartData' 
        ));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'evaluatee_id' => 'required|exists:users,id', //memisahkan validasi
            'academic_year_id' => 'required|exists:academic_years,id',
            'period_month' => 'required',
            'scores' => 'required|array',
            'general_notes' => 'nullable|string'
        ]);


        // Gunakan DB Transaction untuk memastikan data integrity
        DB::transaction(function () use ($request) {   //memanggil method transaction pada facade DB untuk memulai transaksi database. Semua operasi database yang dilakukan di dalam closure ini akan dianggap sebagai satu unit kerja. Jika terjadi error di tengah-tengah, semua perubahan database yang sudah dilakukan akan di-rollback otomatis, sehingga data tetap konsisten.
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

            // Simpan detail penilaian untuk setiap pertanyaan
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

    public function destroy($id)
    {
        $assessment = Assessment::where('id', $id)
                                ->where('evaluator_id', Auth::id())
                                ->firstOrFail();
        
        $assessment->delete();

        return back()->with('success', 'Data penilaian berhasil dihapus / direset.');
    }

    public function printReport(Request $request)
    {
        // Cek apakah guru ini adalah wali kelas dari suatu kelas
        $teacher = Auth::user();
        $classroom = Classroom::where('homeroom_teacher_id', $teacher->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();
        
        $periodMonth = $request->query('period', Carbon::now('Asia/Jakarta')->format('m-Y'));
        
        $assessments = Assessment::where('evaluator_id', $teacher->id)
            ->where('academic_year_id', $activeYear->id)
            ->where('period_month', $periodMonth)
            ->with(['evaluatee', 'details.question.category'])
            ->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'Belum ada data penilaian untuk dicetak pada periode ini.');
        }

        $categories = AssessmentCategory::where('is_active', true)->get();

        $pdf = Pdf::loadView('teacher.assessments.pdf', compact('classroom', 'assessments', 'categories', 'periodMonth', 'activeYear', 'teacher'));
        
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Rapor-Karakter-'.$classroom->name.'.pdf');
    }
}