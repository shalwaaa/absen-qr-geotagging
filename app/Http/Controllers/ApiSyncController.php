<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\ClassMember;

class ApiSyncController extends Controller
{
    public function index()
    {
        $startYear = 2020;
        $endYear = date('Y') + 1;

        for ($y = $startYear; $y <= $endYear; $y++) {
            AcademicYear::firstOrCreate(['name' => $y . '/' . ($y + 1) . ' Ganjil'], ['start_date' => $y . '-07-01', 'end_date' => $y . '-12-31', 'is_active' => false]);
            AcademicYear::firstOrCreate(['name' => $y . '/' . ($y + 1) . ' Genap'], ['start_date' => ($y + 1) . '-01-01', 'end_date' => ($y + 1) . '-06-30', 'is_active' => false]);
        }

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.sync.index', compact('years'));
    }

    public function sync(Request $request)
    {
        // 1. SET LIMIT BIAR GAK TIMEOUT
        set_time_limit(0); // Unlimited time
        ini_set('memory_limit', '1024M'); // Tambah RAM script

        $request->validate(['academic_year_id' => 'required']);
        $yearDB = AcademicYear::findOrFail($request->academic_year_id);
        $apiYear = substr($yearDB->name, 0, 4); 

        // 2. GENERATE PASSWORD HASH SEKALI SAJA (INI KUNCINYA!)
        // Daripada generate 1000x di dalam loop, kita generate 1x di sini.
        $defaultPasswordHash = Hash::make('smakzie123');

        try {
            DB::transaction(function () use ($apiYear, $yearDB, $defaultPasswordHash) {
                
                // Sync Guru
                $this->syncTeachers($apiYear, $defaultPasswordHash);

                // Sync Kelas
                $this->syncClassrooms($apiYear);

                // Sync Siswa
                $this->syncStudents($apiYear, $yearDB->id, $defaultPasswordHash);
            });

            return back()->with('success', "Sukses! Data API Tahun $apiYear berhasil masuk ke Folder " . $yearDB->name);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Sync: ' . $e->getMessage());
        }
    }

    // --- LOGIKA PRIVATE ---

    private function syncTeachers($year, $passwordHash)
{
    $response = Http::timeout(30)->get("https://zieapi.zielabs.id/api/getguru?tahun=" . $year);

    if ($response->successful()) {
        $rawData = $response->json();
        $teachers = $rawData['data'] ?? $rawData;

        foreach ($teachers as $d) {
            $nip = $d['nip'] ?? $d['nuptk'] ?? $d['id'];

            $email = trim($d['email'] ?? '');
            if ($email === '') {
                $email = $nip . '@guru.sekolah.id';
            }

            User::updateOrCreate(
                ['nip_nis' => $nip],
                [
                    'name'     => $d['nama'],
                    'email'    => $email,
                    'password' => $passwordHash,
                    'role'     => 'teacher',
                    'is_piket' => false,
                    'status'   => 'active'
                ]
            );
        }
    }
}


    private function syncClassrooms($year)
    {
        $response = Http::timeout(30)->get("https://zieapi.zielabs.id/api/getkelas?tahun=" . $year);
        
        if ($response->successful()) {
            $classes = $response->json()['data'] ?? [];
            
            foreach ($classes as $d) {
                $grade = 10;
                $namaKelas = strtoupper($d['nama']);
                if (str_starts_with($namaKelas, 'XII')) $grade = 12;
                elseif (str_starts_with($namaKelas, 'XI')) $grade = 11;
                elseif (str_starts_with($namaKelas, 'X')) $grade = 10;

                Classroom::updateOrCreate(
                    ['name' => $d['nama']], 
                    [
                        'grade_level' => $grade,
                        'latitude' => -6.200000, 
                        'longitude' => 106.816666,
                        'radius_meters' => 50
                    ]
                );
            }
        }
    }

    private function syncStudents($year, $academicYearId, $passwordHash)
{
    $response = Http::timeout(120)->get("https://zieapi.zielabs.id/api/getsiswa?tahun=" . $year);

    if ($response->successful()) {
        $students = $response->json()['data'] ?? [];

        // Cache kelas ke memory
        $classMap = Classroom::pluck('id', 'name')->toArray();

        foreach ($students as $d) {

            $nis = $d['no_induk'];

            // Cari kelas
            $namaRombel  = $d['nama_rombel'];
            $classroomId = $classMap[$namaRombel] ?? null;

            // 🔒 NORMALISASI EMAIL (WAJIB)
            $email = strtolower(trim($d['email'] ?? ''));

            if ($email === '') {
                $email = $nis . '@siswa.sekolah.id';
            }

            // Kalau email dipakai siswa lain → fallback
            $emailExists = User::where('email', $email)
                ->where('nip_nis', '!=', $nis)
                ->exists();

            if ($emailExists) {
                $email = $nis . '@siswa.sekolah.id';
            }

            // ✅ UPDATE OR CREATE BERDASARKAN nip_nis
            $user = User::updateOrCreate(
                ['nip_nis' => $nis],
                [
                    'name'         => $d['nama'],
                    'email'        => $email,
                    'password'     => $passwordHash,
                    'role'         => 'student',
                    'classroom_id' => $classroomId,
                    'status'       => 'active'
                ]
            );

            // History kelas
            if ($classroomId) {
                ClassMember::updateOrCreate(
                    [
                        'student_id'       => $user->id,
                        'academic_year_id' => $academicYearId,
                    ],
                    [
                        'classroom_id'          => $classroomId,
                        'is_active'             => true,
                        'attendance_percentage' => 0
                    ]
                );
            }
        }
    }
}

}