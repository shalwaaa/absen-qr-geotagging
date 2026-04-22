<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class SubjectCrudTest extends TestCase
{
    // Ini agar database test di-reset otomatis setiap test selesai, DB asli aman!
    use RefreshDatabase;

    private $admin;

    // Fungsi ini dijalankan otomatis sebelum tiap test dimulai
    protected function setUp(): void
    {
        parent::setUp();

        // KITA GANTI CARA BIKIN USERNYA (Gak pakai factory lagi biar gak error)
        $this->admin = User::firstOrCreate(
            ['email' => 'admintest@sekolah.id'],[
                'name' => 'Admin Tester',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nip_nis' => 'TEST-001', // Wajib diisi karena di database unik
                'status' => 'active'
            ]
        );
    }

    // (Tulisan komentar /** @test */ dihapus agar tidak muncul warning kuning lagi)

    public function test_validasi_gagal_jika_nama_mapel_kosong()
    {
        $response = $this->actingAs($this->admin)->post(route('subjects.store'),[
            'name' => '', // Sengaja dikosongkan
            'grade_level' => 10
        ]);

        // Ekspektasi: Muncul error validasi di kolom 'name'
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('subjects', 0);
    }

    public function test_admin_bisa_input_mapel_baru_berhasil()
    {
        $response = $this->actingAs($this->admin)->post(route('subjects.store'),[
            'name' => 'Matematika Diskrit',
            'code' => 'MTK-D',
            'grade_level' => 11
        ]);

        // Ekspektasi: Redirect berhasil dan data masuk ke database
        $response->assertStatus(302);
        $this->assertDatabaseHas('subjects',[
            'name' => 'Matematika Diskrit',
            'code' => 'MTK-D'
        ]);
    }

    public function test_admin_bisa_update_data_mapel()
    {
        // Bikin data awal
        $subject = Subject::create(['name' => 'Fisika Lama', 'grade_level' => 10]);

        // Aksi Update
        $response = $this->actingAs($this->admin)->put(route('subjects.update', $subject->id),[
            'name' => 'Fisika Kuantum Baru',
            'grade_level' => 12
        ]);

        // Ekspektasi: Di database namanya berubah
        $this->assertDatabaseHas('subjects',[
            'id' => $subject->id,
            'name' => 'Fisika Kuantum Baru',
            'grade_level' => 12
        ]);
    }

    public function test_admin_bisa_hapus_mapel()
    {
        // Bikin data dummy untuk dihapus
        $subject = Subject::create(['name' => 'Mapel Sampah', 'grade_level' => 10]);

        $response = $this->actingAs($this->admin)->delete(route('subjects.destroy', $subject->id));

        // Ekspektasi: Tabel subjects menjadi kosong / data hilang
        $this->assertDatabaseMissing('subjects',[
            'id' => $subject->id
        ]);
    }
}
