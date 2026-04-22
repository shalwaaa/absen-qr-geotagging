<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ApiSyncController;
use App\Http\Controllers\AssessmentCategoryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GamificationAdminController;
use App\Http\Controllers\HeadmasterController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\HomeroomController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentAssessmentController;
use App\Http\Controllers\StudentGamificationController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherAssessmentController;
use App\Http\Controllers\UserController;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Schedule;
use Carbon\Carbon;




Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // LOGIC REDIRECT DASHBOARD

    Route::get('/dashboard', function () {
            $role = Auth::user()->role;

            if ($role == 'teacher') {
                return redirect()->route('teacher.dashboard');
            } 
            
            elseif ($role == 'admin') {
            // 1. Statistik Utama
            $jumlah_siswa = User::where('role', 'student')->count();
            $jumlah_guru  = User::where('role', 'teacher')->count();
            $jumlah_kelas = Classroom::count();
            $jumlah_mapel = Subject::count();

            // 2. LOGIK GRAFIK PER JURUSAN
            // Ambil Tingkat dari Request (Default: Kelas 10)
            $selectedGrade = request('grade', 10); 

            // Daftar Jurusan (Sesuaikan dengan singkatan di nama kelas)
            $majors = ['PPLG', 'TJKT', 'MPLB', 'AKKUL', 'PS'];
            
            // Bulan (6 Bulan Terakhir)
            $months = [];
            $chartLabels = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date;
                $chartLabels[] = $date->translatedFormat('F');
            }

            $charts = []; // Array untuk menampung data semua chart

            // Warna Garis (Variasi Hijau/Bumi)
            $colors = ['#2D5128', '#8DA750', '#E4EB9C', '#d97706', '#1F2937'];

            foreach ($majors as $major) {
                // Cari kelas sesuai Tingkat & Jurusan
                // Contoh: Tingkat 10, Nama mengandung "PPLG"
                $classes = Classroom::where('grade_level', $selectedGrade)
                            ->where('name', 'LIKE', "%$major%")
                            ->get();
                
                $datasets = [];

                foreach ($classes as $idx => $class) {
                    $dataPoints = [];
                    foreach ($months as $month) {
                        // Hitung Hadir
                        $hadir = \App\Models\Attendance::whereHas('meeting.schedule', function($q) use ($class) {
                                    $q->where('classroom_id', $class->id);
                                })
                                ->where('status', 'present')
                                ->whereMonth('scan_time', $month->month)
                                ->whereYear('scan_time', $month->year)
                                ->count();
                        
                        // Hitung Kapasitas (Siswa x Sesi)
                        $siswaCount = $class->students()->where('role', 'student')->count();
                        $sesiCount = \App\Models\Meeting::whereHas('schedule', function($q) use ($class) {
                                    $q->where('classroom_id', $class->id);
                                })
                                ->whereMonth('date', $month->month)
                                ->count();

                        // Hitung Persentase Hadir
                        $max = $siswaCount * $sesiCount;
                        $percentage = ($max > 0) ? round(($hadir / $max) * 100) : 0;
                        $dataPoints[] = $percentage;
                    }

                    // Masukkan ke Dataset Grafik
                    $datasets[] = [
                        'label' => $class->name,
                        'data' => $dataPoints,
                        'borderColor' => $colors[$idx % count($colors)], // Loop warna
                        'backgroundColor' => 'transparent',
                        'borderWidth' => 3,
                        'pointRadius' => 4,
                        'tension' => 0.3
                    ];
                }

                // Simpan data chart per jurusan jika ada kelasnya
                if ($classes->count() > 0) {
                    $charts[$major] = [
                        'labels' => $chartLabels,
                        'datasets' => $datasets
                    ];
                }
            }

            return view('admin.dashboard', compact(
                'jumlah_siswa', 'jumlah_guru', 'jumlah_kelas', 'jumlah_mapel',
                'charts', 'selectedGrade'
            ));
        }
        
    else {
        // LOGIC DATA DASHBOARD SISWA
        
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Hitung Total Hadir Bulan Ini (Status: Present)
        $totalHadir = \App\Models\Attendance::where('student_id', $userId)
                        ->where('status', 'present')
                        ->whereMonth('scan_time', $currentMonth)
                        ->whereYear('scan_time', $currentYear)
                        ->count();

        // 2. Hitung Ketepatan (Persentase Hadir vs Total Pertemuan)
        // anggap Total Pertemuan = Hadir + Sakit + Izin + Alpha
        $totalPertemuan = \App\Models\Attendance::where('student_id', $userId)
                        ->whereMonth('scan_time', $currentMonth)
                        ->whereYear('scan_time', $currentYear)
                        ->count();

        // Hindari pembagian dengan nol
        $persentase = $totalPertemuan > 0 
            ? round(($totalHadir / $totalPertemuan) * 100) 
            : 0;

        return view('student.dashboard', compact('totalHadir', 'persentase'));
    }
    
    })->name('dashboard');

    // ROUTE KHUSUS GURU
    Route::get('/teacher/dashboard', [MeetingController::class, 'index'])->name('teacher.dashboard');
    Route::post('/meetings', [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('/meetings/{id}', [MeetingController::class, 'show'])->name('meetings.show');
    Route::post('/meetings/{id}/toggle', [MeetingController::class, 'toggleStatus'])->name('meetings.toggle');

    // Route Dashboard GURU PIKET 
    Route::get('/teacher/piket', [MeetingController::class, 'piketIndex'])->name('teacher.piket');

    // ROUTE KHUSUS SISWA
    Route::get('/scan', [AttendanceController::class, 'index'])->name('attendance.scan');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    // Route Penilaian (Rapor Karakter) Siswa
    Route::get('/my-character', [StudentAssessmentController::class, 'index'])->name('student.assessments');

    // ROUTE KHUSUS ADMIN (RESOURCES)
    Route::delete('/classrooms/destroy-all', [ClassroomController::class, 'destroyAll'])->name('classrooms.destroyAll');
    Route::resource('classrooms', ClassroomController::class);

    //mata pelajaran
    Route::resource('subjects', SubjectController::class);
    
    // manajemen user
    Route::delete('/users/delete-all', [UserController::class, 'deleteAll'])->name('users.delete_all');
    Route::resource('users', UserController::class);
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    
    // manajemen jadwal
    Route::resource('schedules', ScheduleController::class);
    Route::get('schedules/classroom/{classroom}', [ScheduleController::class, 'classroomShow'])->name('schedules.classroom.show');

    // manajemen cuti
    Route::resource('leaves', LeaveRequestController::class);

    // manajemen homeroom
    Route::get('/homeroom', [HomeroomController::class, 'index'])->name('homeroom.index');
    Route::post('/homeroom/leave/{id}', [HomeroomController::class, 'updateStatus'])->name('homeroom.update');
    // manajemen penilaian sikap
    Route::get('/homeroom/assessments', [TeacherAssessmentController::class, 'index'])->name('teacher.assessments.index');
    Route::post('/homeroom/assessments',[TeacherAssessmentController::class, 'store'])->name('teacher.assessments.store');
    Route::delete('/homeroom/assessments/{id}',[TeacherAssessmentController::class, 'destroy'])->name('teacher.assessments.destroy');
    // Route::post('/homeroom/assessments', [TeacherAssessmentController::class, 'store'])->name('teacher.assessments.store');
    Route::delete('/homeroom/assessments/{id}',[TeacherAssessmentController::class, 'destroy'])->name('teacher.assessments.destroy');
    Route::get('/homeroom/assessments/print', [TeacherAssessmentController::class, 'printReport'])->name('teacher.assessments.print');
    Route::delete('/assessment-categories/{id}', [AssessmentCategoryController::class, 'destroy'])->name('assessment-categories.destroy');
    Route::post('/assessment-questions',[AssessmentCategoryController::class, 'storeQuestion'])->name('assessment-questions.store');
    Route::delete('/assessment-questions/{id}', [AssessmentCategoryController::class, 'destroyQuestion'])->name('assessment-questions.destroy');
    // Route Manajemen Tahun Ajaran
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/academic-years/{id}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    // Promosi Siswa (GA KEPAKE)
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('/promotions', [PromotionController::class, 'process'])->name('promotions.process');
    Route::get('/api/students/{classroomId}', [PromotionController::class, 'getStudents']);
  
    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // untuk regenerate QR Code
    Route::post('/meetings/{id}/regenerate', [MeetingController::class, 'regenerateQr'])->name('meetings.regenerate');

    // Monitoring Guru
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    // untuk memanggil Guru Piket
    Route::post('/monitoring/{id}/panggil', [MonitoringController::class, 'panggilPiket'])->name('monitoring.panggil');

    // Khusus Kepala Sekolah
    Route::get('/headmaster/leaves', [HeadmasterController::class, 'index'])->name('headmaster.index');
    Route::post('/headmaster/leaves/{id}', [HeadmasterController::class, 'updateStatus'])->name('headmaster.update');

    // untuk delete all users (HATI-HATI)
    Route::delete('/users/delete-all', [UserController::class, 'deleteAll'])->name('users.delete_all');

    // Libur
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
    
    // Khusus Sync API LIBUR NASIONAL
    Route::post('/holidays/sync', [HolidayController::class, 'syncNational'])->name('holidays.sync');
    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

    // Kalender Libur
    Route::get('/calendar', [\App\Http\Controllers\HolidayController::class, 'calendar'])->name('calendar.index');

    // ROUTE SINKRONISASI API USER & KELAS
    Route::prefix('sync')->group(function () {
        // Halaman utama sinkronisasi
        Route::get('/', [ApiSyncController::class, 'index'])->name('sync.index');
        
        // Proses sinkronisasi (POST request)
        Route::post('/data', [ApiSyncController::class, 'sync'])->name('sync.data');
        
        // Progress tracking (AJAX)
        Route::get('/progress', [ApiSyncController::class, 'progress'])->name('sync.progress');
        
        // Cleanup tahun kosong
        Route::post('/cleanup', [ApiSyncController::class, 'cleanup'])->name('sync.cleanup');
        
        // Debug API
        Route::get('/debug', [ApiSyncController::class, 'debugApi'])->name('sync.debug');
    });
    //END ROUTE SINKRONISASI

    // ROUTE PROFILE (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/sync/quick', [ApiSyncController::class, 'quickSync'])->name('sync.quick');

    ////////////////////////////////////////////////////////////////////
    //ROUTE BARU
    ///////////////////////////////////////////////////////////////////

    // Route Manajemen Kategori Penilaian Sikap
    Route::get('/assessment-categories',[AssessmentCategoryController::class, 'index'])->name('assessment-categories.index');
    Route::post('/assessment-categories', [AssessmentCategoryController::class, 'store'])->name('assessment-categories.store');
    Route::put('/assessment-categories/{id}',[AssessmentCategoryController::class, 'update'])->name('assessment-categories.update');
    Route::post('/assessment-categories/{id}/toggle', [AssessmentCategoryController::class, 'toggleStatus'])->name('assessment-categories.toggle');
    
    // GAMIFIKASI & INTEGRITAS
    Route::get('/gamification',[GamificationAdminController::class, 'index'])->name('gamification.index');
    Route::post('/gamification/rules',[GamificationAdminController::class, 'storeRule'])->name('gamification.rules.store');
    Route::delete('/gamification/rules/{id}',[GamificationAdminController::class, 'destroyRule'])->name('gamification.rules.destroy');
    Route::post('/gamification/items', [GamificationAdminController::class, 'storeItem'])->name('gamification.items.store');
    Route::delete('/gamification/items/{id}',[GamificationAdminController::class, 'destroyItem'])->name('gamification.items.destroy');
    
    // ROUTE DOMPET SISWA 
    Route::get('/my-wallet', [StudentGamificationController::class, 'index'])->name('student.wallet');
    Route::post('/my-wallet/purchase/{id}',[StudentGamificationController::class, 'purchase'])->name('student.wallet.purchase');
});

// Route untuk testing
require __DIR__.'/auth.php';