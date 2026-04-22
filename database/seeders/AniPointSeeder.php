<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PointLedger;
use Illuminate\Support\Facades\DB;

class AniPointSeeder extends Seeder
{
    public function run()
    {
        // 1. Cari akun Ani berdasarkan NIS
        $ani = User::where('nip_nis', '12345')->where('role', 'student')->first();

        if (!$ani) {
            $this->command->error("Akun Ani dengan NIS 12345 tidak ditemukan! Cek kembali NIS-nya.");
            return;
        }

        // 2. PINDAHKAN KE SINI (Di luar transaction)
        $bonusPoin = 500;

        // 3. Masukkan $bonusPoin ke dalam 'use' agar bisa dibaca di dalam fungsi
        DB::transaction(function () use ($ani, $bonusPoin) {
            
            // Tambahkan ke saldo utama
            $ani->increment('integrity_points', $bonusPoin);

            // Catat di Buku Besar (Ledger)
            PointLedger::create([
                'user_id' => $ani->id,
                'transaction_type' => 'EARN',
                'amount' => $bonusPoin,
                'current_balance' => $ani->fresh()->integrity_points,
                'description' => 'Bonus Saldo Awal (Test Demo Skripsi)'
            ]);
        });

        $this->command->info("SUKSES! Berhasil menyuntikkan {$bonusPoin} Poin ke akun {$ani->name}.");
    }
}