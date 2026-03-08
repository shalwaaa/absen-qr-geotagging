<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // --- MUATAN A (NASIONAL) ---
            ['code' => 'PAI',   'name' => 'Pendidikan Agama dan Budi Pekerti', 'grade_level' => 10],
            ['code' => 'PPKN',  'name' => 'Pendidikan Pancasila dan Kewarganegaraan', 'grade_level' => 10],
            ['code' => 'IND',   'name' => 'Bahasa Indonesia', 'grade_level' => 10],
            ['code' => 'MTK',   'name' => 'Matematika', 'grade_level' => 10],
            ['code' => 'SEJ',   'name' => 'Sejarah Indonesia', 'grade_level' => 10],
            ['code' => 'ING',   'name' => 'Bahasa Inggris', 'grade_level' => 10],

            // --- MUATAN B (KEWILAYAHAN) ---
            ['code' => 'SBK',   'name' => 'Seni Budaya', 'grade_level' => 10],
            ['code' => 'PJOK',  'name' => 'Pendidikan Jasmani, Olahraga dan Kesehatan', 'grade_level' => 10],
            ['code' => 'BSD',   'name' => 'Bahasa Sunda', 'grade_level' => 10],

            // --- DASAR BIDANG KEAHLIAN (C1) ---
            ['code' => 'SIMDIG', 'name' => 'Simulasi dan Komunikasi Digital', 'grade_level' => 10],
            ['code' => 'FIS',    'name' => 'Fisika', 'grade_level' => 10],
            ['code' => 'KIM',    'name' => 'Kimia', 'grade_level' => 10],
            ['code' => 'IPAS',   'name' => 'Projek IPAS', 'grade_level' => 10],

            // --- PRODUKTIF RPL (PPLG) ---
            ['code' => 'PBO',    'name' => 'Pemrograman Berorientasi Objek', 'grade_level' => 11],
            ['code' => 'WEB',    'name' => 'Pemrograman Web dan Perangkat Bergerak', 'grade_level' => 11],
            ['code' => 'BASDAT', 'name' => 'Basis Data', 'grade_level' => 11],
            ['code' => 'PKK',    'name' => 'Produk Kreatif dan Kewirausahaan', 'grade_level' => 12],

            // --- PRODUKTIF TKJ (TJKT) ---
            ['code' => 'JARDAS', 'name' => 'Komputer dan Jaringan Dasar', 'grade_level' => 10],
            ['code' => 'ASJ',    'name' => 'Administrasi Sistem Jaringan', 'grade_level' => 11],
            ['code' => 'AIJ',    'name' => 'Administrasi Infrastruktur Jaringan', 'grade_level' => 11],
            ['code' => 'TLJ',    'name' => 'Teknologi Layanan Jaringan', 'grade_level' => 12],
            
            // --- PRODUKTIF OTKP (MPLB) ---
            ['code' => 'KEPEGAWAIAN', 'name' => 'Otomatisasi Tata Kelola Kepegawaian', 'grade_level' => 11],
            ['code' => 'KEUANGAN',    'name' => 'Otomatisasi Tata Kelola Keuangan', 'grade_level' => 11],
            ['code' => 'SARPRAS',     'name' => 'Otomatisasi Tata Kelola Sarana dan Prasarana', 'grade_level' => 12],
            
             // --- PRODUKTIF AKUNTANSI (AKKUL) ---
            ['code' => 'MYOB',        'name' => 'Komputer Akuntansi (MYOB)', 'grade_level' => 11],
            ['code' => 'PAJAK',       'name' => 'Administrasi Pajak', 'grade_level' => 11],
            ['code' => 'MANUFAKTUR',  'name' => 'Akuntansi Perusahaan Manufaktur', 'grade_level' => 12],
        ];

        foreach ($subjects as $s) {
            // Gunakan updateOrCreate agar tidak duplikat jika dijalankan 2x
            Subject::updateOrCreate(
                ['code' => $s['code']], // Cek berdasarkan Kode Mapel
                [
                    'name' => $s['name'],
                    'grade_level' => $s['grade_level']
                ]
            );
        }
        
        $this->command->info('Berhasil membuat 20+ Mata Pelajaran lengkap!');
    }
}