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
        $assessments = Assessment::where('evaluatee_id', $studentId)
                        ->with(['evaluator', 'details.question.category', 'academicYear'])
                        ->orderBy('assessment_date', 'desc')
                        ->get();

        // 2. Ambil penilaian TERBARU untuk ditampilkan di Grafik Radar
        $latestAssessment = $assessments->first();
        
        $chartLabels = [];
        $chartData = [];

        // 3. Jika ada penilaian terbaru, kelompokkan detailnya berdasarkan kategori
        if ($latestAssessment) {
            $groupedByCategory = $latestAssessment->details->groupBy(function($detail) {
                return $detail->question->category->name ?? 'Lain-lain';
            });

            // Loop setiap kategori yang sudah dikelompokkan
            foreach ($groupedByCategory as $categoryName => $details) {
                $chartLabels[] = $categoryName;
                
                // Hitung rata-rata skor untuk kategori ini
                $averageScore = $details->avg('score');
                
                $chartData[] = round($averageScore, 2);
            }
        }

        return view('student.assessments.index', compact('assessments', 'latestAssessment', 'chartLabels', 'chartData'));
    }
}