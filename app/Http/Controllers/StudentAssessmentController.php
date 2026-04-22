<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAssessmentController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();

        // 1. Ambil semua riwayat penilaian siswa ini, urutkan dari yang terbaru
        // PERBAIKAN: Ubah 'details.category' menjadi 'details.question.category'
        $assessments = Assessment::where('evaluatee_id', $studentId)
                        ->with(['evaluator', 'details.question.category', 'academicYear'])
                        ->orderBy('assessment_date', 'desc')
                        ->get();

        // 2. Ambil penilaian TERBARU untuk ditampilkan di Grafik Radar
        $latestAssessment = $assessments->first();
        
        $chartLabels = [];
        $chartData = [];

        if ($latestAssessment) {
            foreach ($latestAssessment->details as $detail) {
                // PERBAIKAN: Akses melalui question dulu
                $chartLabels[] = $detail->question->category->name ?? 'Unknown';
                $chartData[] = $detail->score;
            }
        }

        return view('student.assessments.index', compact('assessments', 'latestAssessment', 'chartLabels', 'chartData'));
    }
}