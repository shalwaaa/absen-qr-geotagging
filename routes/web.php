<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ApiSyncController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeroomController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SubjectController;
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
            // --- LOGIC UNTUK DATA DASHBOARD ADMIN ---
            
            // 1. Hitung Data Statistik
            $jumlah_siswa = User::where('role', 'student')->count();
            $jumlah_guru  = User::where('role', 'teacher')->count();
            $jumlah_kelas = Classroom::count();
            $jumlah_mapel = Subject::count();

            // 2. Ambil Jadwal Hari Ini (Limit 5 saja biar rapi)
            Carbon::setLocale('id');
            $hari_ini = Carbon::now()->isoFormat('dddd'); // Senin, Selasa...
            
            $schedules_today = Schedule::with(['teacher', 'subject', 'classroom'])
                                ->where('day', $hari_ini)
                                ->orderBy('start_time')
                                ->take(5)
                                ->get();

            // 3. Ambil List Kelas & Mapel (Untuk Widget Kanan)
            $classrooms_list = Classroom::latest()->take(4)->get();
            $subjects_list   = Subject::inRandomOrder()->take(8)->get();

            return view('admin.dashboard', compact(
                'jumlah_siswa', 
                'jumlah_guru', 
                'jumlah_kelas', 
                'jumlah_mapel',
                'schedules_today',
                'classrooms_list',
                'subjects_list'
            ));
        } 
        
        else {
            return view('student.dashboard');
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

    // ROUTE KHUSUS ADMIN (RESOURCES)
    Route::resource('classrooms', ClassroomController::class);
    Route::resource('subjects', SubjectController::class);
    
    Route::resource('users', UserController::class);
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    
    Route::resource('schedules', ScheduleController::class);

    Route::resource('leaves', LeaveRequestController::class);

    Route::get('/homeroom', [HomeroomController::class, 'index'])->name('homeroom.index');
    Route::post('/homeroom/leave/{id}', [HomeroomController::class, 'updateStatus'])->name('homeroom.update');

    // Route Manajemen Tahun Ajaran
    Route::resource('academic-years', AcademicYearController::class);
    Route::post('academic-years/{id}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    // Route Promosi Siswa
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('/promotions', [PromotionController::class, 'process'])->name('promotions.process');
    Route::get('/api/students/{classroomId}', [PromotionController::class, 'getStudents']);
  
    // Route Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Route Sinkronisasi
    Route::get('/sync-data', [ApiSyncController::class, 'index'])->name('sync.index');
    Route::post('/sync-data', [ApiSyncController::class, 'sync'])->name('sync.process');

    // Route Hapus Tahun Ajaran Kosong
    Route::delete('/admin/academic-year/cleanup', 
    [ApiSyncController::class, 'deleteEmptyAcademicYears']
    )->name('academic-years.cleanup');

    // ROUTE PROFILE (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';