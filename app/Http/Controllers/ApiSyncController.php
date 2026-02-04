<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\ClassMember;

class ApiSyncController extends Controller
{
    private const BATCH_SIZE = 100;
    
    public function index()
    {
        try {
            // Buat tahun ajaran dengan format Indonesia (2023/2024, 2024/2025)
            $currentYear = date('Y');
            $yearsToCreate = [];
            
            // Tahun ajaran biasanya format: 2023/2024
            for ($y = 2020; $y <= $currentYear + 1; $y++) {
                $yearName = "{$y}/" . ($y + 1);
                $yearsToCreate[] = [
                    'name' => $yearName,
                    'start_date' => $y . '-07-01', // Juli
                    'end_date' => ($y + 1) . '-06-30', // Juni tahun berikutnya
                    'is_active' => false
                ];
            }
            
            foreach ($yearsToCreate as $yearData) {
                AcademicYear::firstOrCreate(
                    ['name' => $yearData['name']],
                    $yearData
                );
            }
            
            $years = AcademicYear::orderBy('name', 'desc')->get();
            return view('admin.sync.index', compact('years'));
            
        } catch (\Exception $e) {
            Log::error('SYNC INDEX ERROR: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat halaman: ' . $e->getMessage());
        }
    }
    
    /**
     * Main Sync Process - Dipanggil dari AJAX
     */
    public function sync(Request $request)
    {
        try {
            // Ambil tahun dari request, default tahun ajaran terakhir
            $selectedYear = $request->input('year', $this->getLatestAcademicYear());
            
            // Konversi format tahun (2024/2025 → 2024 untuk API)
            $apiYear = $this->extractApiYear($selectedYear);
            
            Log::info("🔄 Memulai sinkronisasi untuk tahun: {$selectedYear} (API: {$apiYear})");
            
            // Cari atau buat tahun ajaran
            $academicYear = AcademicYear::firstOrCreate(
                ['name' => $selectedYear],
                [
                    'start_date' => $this->getStartDate($apiYear),
                    'end_date' => $this->getEndDate($apiYear),
                    'is_active' => false
                ]
            );
            
            // Step 1: Sync Guru
            $guruResult = $this->syncGuru($apiYear);
            if (!$guruResult['success']) {
                return response()->json($guruResult, 500);
            }
            
            // Step 2: Sync Kelas
            $kelasResult = $this->syncKelas($apiYear);
            if (!$kelasResult['success']) {
                return response()->json($kelasResult, 500);
            }
            
            // Step 3: Sync Siswa
            $siswaResult = $this->syncSiswa($apiYear, $academicYear->id);
            
            return response()->json($siswaResult);
            
        } catch (\Exception $e) {
            Log::error('MAIN SYNC ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync gagal: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function quickSync(Request $request)
    {
        try {
            $academicYear = AcademicYear::where('is_active', true)->first();
            
            if (!$academicYear) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tahun ajaran aktif. Silakan set tahun ajaran aktif terlebih dahulu.'
                ], 400);
            }
            
            $apiYear = $this->extractApiYear($academicYear->name);
            
            // Langsung jalankan sync siswa
            return $this->syncSiswa($apiYear, $academicYear->id);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal sync cepat: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Step 1: Sync Guru dengan validasi error API
     */
    private function syncGuru(string $apiYear)
    {
        Log::info("🔄 Sync Guru untuk tahun: {$apiYear}");
        
        try {
            $response = Http::timeout(60)
                ->get("https://zieapi.zielabs.id/api/getguru", ['tahun' => $apiYear]);
            
            if (!$response->successful()) {
                throw new \Exception("API Guru gagal: " . $response->status());
            }
            
            $data = $response->json();
            
            // Validasi response API
            if (isset($data['status']) && $data['status'] === false) {
                throw new \Exception("API Guru error: " . ($data['message'] ?? 'Data tidak ditemukan'));
            }
            
            if (isset($data['message']) && strpos(strtolower($data['message']), 'tidak ditemukan') !== false) {
                throw new \Exception("API Guru: " . $data['message']);
            }
            
            $teachers = $data['data'] ?? $data;
            $count = 0;
            
            if (is_array($teachers) && !empty($teachers)) {
                foreach ($teachers as $teacher) {
                    if (empty($teacher['nama'])) continue;
                    
                    $nip = $teacher['nip'] ?? $teacher['nuptk'] ?? $teacher['id'] ?? 'guru_' . uniqid();
                    $nip = trim($nip);
                    
                    if (empty($nip)) {
                        $nip = 'guru_' . uniqid();
                    }
                    
                    // Buat email sederhana
                    $email = strtolower(preg_replace('/[^a-z0-9]/', '', $teacher['nama'])) 
                           . '.' . $nip 
                           . '@guru.sekolah.id';
                    
                    User::updateOrCreate(
                        ['nip_nis' => $nip],
                        [
                            'name' => trim($teacher['nama']),
                            'email' => $email,
                            'password' => Hash::make('smakzie123'),
                            'role' => 'teacher'
                        ]
                    );
                    
                    $count++;
                }
                
                Log::info("✓ Guru: {$count} data");
                
                return [
                    'success' => true,
                    'progress' => 33,
                    'message' => "Guru berhasil: {$count} data",
                    'step' => 'kelas'
                ];
            } else {
                Log::warning("Data guru kosong untuk tahun {$apiYear}");
                return [
                    'success' => true,
                    'progress' => 33,
                    'message' => "Tidak ada data guru untuk tahun {$apiYear}",
                    'step' => 'kelas'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('GURU SYNC ERROR: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Step 2: Sync Kelas dengan validasi error API
     */
    private function syncKelas(string $apiYear)
    {
        Log::info("🔄 Sync Kelas untuk tahun: {$apiYear}");
        
        try {
            $response = Http::timeout(60)
                ->get("https://zieapi.zielabs.id/api/getkelas", ['tahun' => $apiYear]);
            
            if (!$response->successful()) {
                Log::error("API Kelas gagal: " . $response->status() . " - " . $response->body());
                throw new \Exception("API Kelas gagal: " . $response->status());
            }
            
            $data = $response->json();
            
            // Validasi response API
            if (isset($data['status']) && $data['status'] === false) {
                throw new \Exception("API Kelas error: " . ($data['message'] ?? 'Data tidak ditemukan'));
            }
            
            if (isset($data['message']) && strpos(strtolower($data['message']), 'tidak ditemukan') !== false) {
                throw new \Exception("API Kelas: " . $data['message']);
            }
            
            // LOG RESPONSE UNTUK DEBUG
            Log::info("Kelas API Response: " . json_encode($data, JSON_PRETTY_PRINT));
            
            // Coba berbagai kemungkinan struktur data
            $classes = [];
            
            if (isset($data['data']) && is_array($data['data'])) {
                $classes = $data['data'];
            } elseif (isset($data['rombel']) && is_array($data['rombel'])) {
                $classes = $data['rombel'];
            } elseif (is_array($data) && !isset($data['message'])) {
                $classes = $data;
            }
            
            Log::info("Jumlah kelas ditemukan: " . count($classes));
            
            $count = 0;
            
            if (is_array($classes) && !empty($classes)) {
                foreach ($classes as $index => $class) {
                    // LOG untuk debugging tiap kelas
                    Log::debug("Kelas data #{$index}: " . json_encode($class));
                    
                    // Coba berbagai kemungkinan key untuk nama kelas
                    $namaKelas = null;
                    
                    if (isset($class['nama']) && !empty($class['nama'])) {
                        $namaKelas = $class['nama'];
                    } elseif (isset($class['nama_kelas']) && !empty($class['nama_kelas'])) {
                        $namaKelas = $class['nama_kelas'];
                    } elseif (isset($class['nama_rombel']) && !empty($class['nama_rombel'])) {
                        $namaKelas = $class['nama_rombel'];
                    } elseif (isset($class['kelas']) && !empty($class['kelas'])) {
                        $namaKelas = $class['kelas'];
                    } elseif (isset($class['rombel']) && !empty($class['rombel'])) {
                        $namaKelas = $class['rombel'];
                    }
                    
                    if (empty($namaKelas)) {
                        Log::warning("Nama kelas kosong untuk data: " . json_encode($class));
                        continue;
                    }
                    
                    $nama = strtoupper(trim($namaKelas));
                    Log::info("Memproses kelas: {$nama}");
                    
                    // Detect grade level dari nama kelas
                    $gradeLevel = $this->detectGradeLevel($nama);
                    
                    Classroom::updateOrCreate(
                        ['name' => $nama],
                        [
                            'grade_level' => $gradeLevel,
                            'latitude' => -6.82681,
                            'longitude' => 107.13714,
                            'radius_meters' => 50,
                            'updated_at' => now()
                        ]
                    );
                    
                    $count++;
                    
                    // Log progress setiap 10 kelas
                    if ($count % 10 === 0) {
                        Log::info("Kelas diproses: {$count}");
                    }
                }
                
                Log::info("✓ Kelas: {$count} data berhasil disinkronisasi");
                
                return [
                    'success' => true,
                    'progress' => 66,
                    'message' => "Kelas berhasil: {$count} data",
                    'step' => 'siswa'
                ];
            } else {
                Log::warning("Data kelas kosong atau bukan array untuk tahun {$apiYear}");
                return [
                    'success' => true,
                    'progress' => 66,
                    'message' => "Tidak ada data kelas untuk tahun {$apiYear}",
                    'step' => 'siswa'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('KELAS SYNC ERROR: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Step 3: Sync Siswa dengan validasi error API yang diperbaiki
     */
    private function syncSiswa(string $apiYear, int $academicYearId)
    {
        Log::info("🔄 Sync Siswa untuk tahun: {$apiYear}, Academic Year ID: {$academicYearId}");
        
        try {
            // Ambil mapping kelas
            $classMap = Classroom::pluck('id', 'name')->mapWithKeys(function ($item, $key) {
                return [strtoupper(trim($key)) => $item];
            })->toArray();
            
            Log::info("Class map: " . json_encode($classMap));
            
            $studentCount = 0;
            $totalProcessed = 0;
            
            // Nonaktifkan siswa lama untuk tahun ajaran ini
            ClassMember::where('academic_year_id', $academicYearId)
                      ->update(['is_active' => false]);
            
            $page = 1;
            $hasMore = true;
            $maxPages = 10; // Batasi halaman untuk efisiensi
            
            Log::info("Memulai sinkronisasi siswa...");
            
            while ($hasMore && $page <= $maxPages) {
                Log::info("📄 Mengambil data siswa halaman: {$page}");
                
                try {
                    $response = Http::timeout(60)->get('https://zieapi.zielabs.id/api/getsiswa', [
                        'tahun' => $apiYear,
                        'page' => $page
                    ]);
                    
                    if (!$response->successful()) {
                        Log::warning("Siswa page {$page} gagal: " . $response->status() . " - " . $response->body());
                        break;
                    }
                    
                    $data = $response->json();
                    
                    // Validasi response API - PERBAIKAN DISINI
                    if (isset($data['status']) && $data['status'] === false) {
                        Log::error("API Siswa error: " . ($data['message'] ?? 'Data tidak ditemukan'));
                        $hasMore = false;
                        throw new \Exception("API Siswa: " . ($data['message'] ?? 'Data tidak ditemukan'));
                    }
                    
                    if (isset($data['message']) && strpos(strtolower($data['message']), 'tidak ditemukan') !== false) {
                        Log::warning("API Siswa: " . $data['message']);
                        $hasMore = false;
                        break;
                    }
                    
                    // LOG RESPONSE UNTUK DEBUG
                    Log::debug("Siswa API Response page {$page}: " . json_encode($data, JSON_PRETTY_PRINT));
                    
                    // Handle berbagai format respons API
                    $students = [];
                    if (isset($data['data']) && is_array($data['data'])) {
                        $students = $data['data'];
                    } elseif (isset($data['siswa']) && is_array($data['siswa'])) {
                        $students = $data['siswa'];
                    } elseif (is_array($data) && !isset($data['message']) && !isset($data['status'])) {
                        $students = $data;
                    } else {
                        Log::warning("Format data siswa tidak dikenal");
                        $students = [];
                    }
                    
                    if (empty($students) || !is_array($students)) {
                        Log::info("Tidak ada data siswa di halaman {$page}");
                        $hasMore = false;
                        break;
                    }
                    
                    Log::info("Memproses " . count($students) . " siswa di halaman {$page}");
                    
                    foreach ($students as $index => $student) {
                        // Skip jika bukan array atau error message
                        if (!is_array($student) || isset($student['message'])) {
                            continue;
                        }
                        
                        $totalProcessed++;
                        
                        // LOG untuk debugging tiap siswa
                        Log::debug("Siswa data #{$index}: " . json_encode($student));
                        
                        // Cek berbagai kemungkinan key untuk NIS
                        $nis = null;
                        if (isset($student['no_induk']) && !empty($student['no_induk'])) {
                            $nis = trim($student['no_induk']);
                        } elseif (isset($student['nis']) && !empty($student['nis'])) {
                            $nis = trim($student['nis']);
                        } elseif (isset($student['nisn']) && !empty($student['nisn'])) {
                            $nis = trim($student['nisn']);
                        } elseif (isset($student['id']) && !empty($student['id'])) {
                            $nis = trim($student['id']);
                        }
                        
                        // Cek berbagai kemungkinan key untuk nama
                        $nama = null;
                        if (isset($student['nama']) && !empty($student['nama'])) {
                            $nama = trim($student['nama']);
                        } elseif (isset($student['nama_siswa']) && !empty($student['nama_siswa'])) {
                            $nama = trim($student['nama_siswa']);
                        } elseif (isset($student['name']) && !empty($student['name'])) {
                            $nama = trim($student['name']);
                        }
                        
                        if (empty($nis) || empty($nama)) {
                            Log::warning("Data siswa tidak lengkap - NIS: {$nis}, Nama: {$nama}");
                            continue;
                        }
                        
                        // Cek berbagai kemungkinan key untuk kelas/rombel
                        $namaRombel = '';
                        if (isset($student['nama_rombel']) && !empty($student['nama_rombel'])) {
                            $namaRombel = strtoupper(trim($student['nama_rombel']));
                        } elseif (isset($student['kelas']) && !empty($student['kelas'])) {
                            $namaRombel = strtoupper(trim($student['kelas']));
                        } elseif (isset($student['rombel']) && !empty($student['rombel'])) {
                            $namaRombel = strtoupper(trim($student['rombel']));
                        } elseif (isset($student['class']) && !empty($student['class'])) {
                            $namaRombel = strtoupper(trim($student['class']));
                        }
                        
                        $classId = null;
                        if (!empty($namaRombel)) {
                            $classId = $classMap[$namaRombel] ?? null;
                            if (!$classId) {
                                Log::warning("Kelas '{$namaRombel}' tidak ditemukan untuk siswa {$nama} (NIS: {$nis})");
                            }
                        }
                        
                        // Generate email
                        $email = $this->generateEmail($nama, $nis, 'siswa.sekolah.id');
                        
                        // Pastikan NIS tidak kosong
                        if (empty($nis) || $nis === ' ') {
                            $nis = 'siswa_' . uniqid();
                        }
                        
                        // Update atau create user
                        $user = User::updateOrCreate(
                            ['nip_nis' => $nis],
                            [
                                'name' => $nama,
                                'email' => $email,
                                'password' => Hash::make('smakzie123'),
                                'role' => 'student',
                                'classroom_id' => $classId,
                                'updated_at' => now()
                            ]
                        );
                        
                        // Tambah ke class member jika ada kelas
                        if ($classId) {
                            ClassMember::updateOrCreate(
                                [
                                    'student_id' => $user->id,
                                    'academic_year_id' => $academicYearId
                                ],
                                [
                                    'classroom_id' => $classId,
                                    'is_active' => true,
                                    'attendance_percentage' => 0,
                                    'updated_at' => now()
                                ]
                            );
                        } else {
                            Log::warning("Siswa {$user->name} ({$nis}) tidak dimasukkan ke kelas manapun");
                        }
                        
                        $studentCount++;
                        
                        // Log progress setiap 50 siswa
                        if ($studentCount % 50 === 0) {
                            Log::info("Siswa diproses: {$studentCount}");
                        }
                    }
                    
                    // Cek pagination
                    if (isset($data['meta']['last_page']) && $page >= $data['meta']['last_page']) {
                        Log::info("Telah mencapai halaman terakhir: {$data['meta']['last_page']}");
                        $hasMore = false;
                    } elseif (isset($data['last_page']) && $page >= $data['last_page']) {
                        Log::info("Telah mencapai halaman terakhir: {$data['last_page']}");
                        $hasMore = false;
                    } elseif (count($students) < 1) {
                        // Jika data kurang dari 1, mungkin sudah habis
                        $hasMore = false;
                    }
                    
                    $page++;
                    
                    // Delay kecil untuk mencegah overload
                    usleep(100000); // 100ms
                    
                } catch (\Exception $e) {
                    Log::error("Error pada halaman siswa {$page}: " . $e->getMessage());
                    $hasMore = false;
                }
            }
            
            Log::info("✓ Siswa: {$studentCount} dari {$totalProcessed} data berhasil disinkronisasi");
            
            return [
                'success' => true,
                'progress' => 100,
                'message' => "Siswa berhasil: {$studentCount} data",
                'step' => 'complete',
                'summary' => [
                    'siswa' => $studentCount
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('SISWA SYNC ERROR: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Helper: Generate email
     */
    private function generateEmail(string $name, string $identifier, string $domain): string
    {
        $cleanName = strtolower(preg_replace('/[^a-z]/', '', $name));
        if (empty($cleanName)) {
            $cleanName = 'user';
        }
        
        $baseEmail = substr($cleanName, 0, 10) . '.' . $identifier . '@' . $domain;
        $baseEmail = str_replace([' ', '@@', '..'], ['', '@', '.'], $baseEmail);
        
        return $baseEmail;
    }
    
    /**
     * Helper: Detect grade level dari nama kelas
     */
    private function detectGradeLevel(string $className): int
    {
        if (str_starts_with($className, 'XII') || preg_match('/\b12\b/', $className)) return 12;
        if (str_starts_with($className, 'XI') || preg_match('/\b11\b/', $className)) return 11;
        if (str_starts_with($className, 'X') || preg_match('/\b10\b/', $className)) return 10;
        return 10;
    }
    
    /**
     * Helper: Ambil tahun untuk API dari format tahun ajaran
     */
    private function extractApiYear(string $academicYear): string
    {
        // Format: 2023/2024 → ambil 2023
        if (strpos($academicYear, '/') !== false) {
            return explode('/', $academicYear)[0];
        }
        
        // Coba tahun sebelumnya jika tahun sekarang tidak ada data
        $currentYear = date('Y');
        if ($academicYear == $currentYear) {
            // Coba tahun-tahun sebelumnya
            $tryYears = [$currentYear, $currentYear - 1, $currentYear - 2];
            
            // Test API untuk tahun-tahun ini
            foreach ($tryYears as $year) {
                Log::info("Testing API year: {$year}");
                try {
                    $test = Http::timeout(10)->get('https://zieapi.zielabs.id/api/getguru', ['tahun' => $year]);
                    if ($test->successful()) {
                        $data = $test->json();
                        if (!isset($data['message']) || strpos($data['message'], 'tidak ditemukan') === false) {
                            return (string)$year;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        
        return $academicYear;
    }
    
    /**
     * Helper: Dapatkan tahun ajaran terbaru
     */
    private function getLatestAcademicYear(): string
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        // Jika bulan Juli-Desember, tahun ajaran: tahun ini/tahun+1
        // Jika bulan Januari-Juni, tahun ajaran: tahun-1/tahun ini
        if ($currentMonth >= 7) {
            return "{$currentYear}/" . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . "/{$currentYear}";
        }
    }
    
    /**
     * Helper: Dapatkan tanggal mulai berdasarkan tahun
     */
    private function getStartDate(string $year): string
    {
        return $year . '-07-01';
    }
    
    /**
     * Helper: Dapatkan tanggal selesai berdasarkan tahun
     */
    private function getEndDate(string $year): string
    {
        return ($year + 1) . '-06-30';
    }
    
    /**
     * Progress tracking
     */
    public function progress(Request $request)
    {
        return response()->json([
            'success' => true,
            'progress' => 0,
            'message' => 'Sync belum dimulai'
        ]);
    }
    
    /**
     * Debug endpoint untuk testing API
     */
    public function debugApi(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            
            // Coba berbagai kemungkinan tahun
            $testYears = [$year, $year - 1, $year - 2, $year . '1', $year . '2'];
            
            Log::info("🔍 Debug API untuk tahun: {$year}");
            
            $results = [];
            
            foreach ($testYears as $testYear) {
                Log::info("Testing tahun: {$testYear}");
                
                try {
                    // Test API Guru
                    $teacherResponse = Http::timeout(15)->get('https://zieapi.zielabs.id/api/getguru', ['tahun' => $testYear]);
                    
                    // Test API Kelas
                    $classResponse = Http::timeout(15)->get('https://zieapi.zielabs.id/api/getkelas', ['tahun' => $testYear]);
                    
                    // Test API Siswa
                    $studentResponse = Http::timeout(15)->get('https://zieapi.zielabs.id/api/getsiswa', [
                        'tahun' => $testYear,
                        'page' => 1
                    ]);
                    
                    $results[$testYear] = [
                        'guru' => [
                            'status' => $teacherResponse->status(),
                            'successful' => $teacherResponse->successful(),
                            'has_data' => $teacherResponse->successful() ? !empty($teacherResponse->json()) : false
                        ],
                        'kelas' => [
                            'status' => $classResponse->status(),
                            'successful' => $classResponse->successful(),
                            'has_data' => $classResponse->successful() ? !empty($classResponse->json()) : false
                        ],
                        'siswa' => [
                            'status' => $studentResponse->status(),
                            'successful' => $studentResponse->successful(),
                            'has_data' => $studentResponse->successful() ? !empty($studentResponse->json()) : false
                        ]
                    ];
                    
                } catch (\Exception $e) {
                    $results[$testYear] = ['error' => $e->getMessage()];
                }
                
                usleep(500000); // 0.5 detik delay antar request
            }
            
            return response()->json([
                'success' => true,
                'test_years' => $testYears,
                'results' => $results,
                'recommended_year' => $this->findBestYear($results)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper: Cari tahun terbaik dari hasil debug
     */
    private function findBestYear(array $results): ?string
    {
        foreach ($results as $year => $data) {
            if (!isset($data['error'])) {
                $hasGuru = $data['guru']['successful'] && $data['guru']['has_data'];
                $hasKelas = $data['kelas']['successful'] && $data['kelas']['has_data'];
                $hasSiswa = $data['siswa']['successful'] && $data['siswa']['has_data'];
                
                if ($hasGuru && $hasKelas && $hasSiswa) {
                    return $year;
                }
            }
        }
        
        return null;
    }
    
    public function cleanup(Request $request)
    {
        try {
            // Cari tahun yang tidak memiliki class members aktif
            $emptyYears = AcademicYear::whereDoesntHave('classMembers', function($query) {
                $query->where('is_active', true);
            })->get();
            
            $count = 0;
            foreach ($emptyYears as $year) {
                if (!$year->is_active) {
                    $year->delete();
                    $count++;
                }
            }
            
            // Return JSON untuk AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menghapus {$count} tahun ajaran kosong."
                ]);
            }
            
            return redirect()->route('sync.index')
                ->with('success', "Berhasil menghapus {$count} tahun ajaran kosong.");
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('sync.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}