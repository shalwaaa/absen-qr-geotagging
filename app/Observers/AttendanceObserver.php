<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\PointRule;
use App\Models\PointLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Wajib import Log

class AttendanceObserver
{
    /**
     * Handle the Attendance "created" event.
     */
    public function created(Attendance $attendance): void
    {
        try {
            $user = $attendance->student; // Asumsi relasinya bernama student
            
            if (!$user) return; // Jaga-jaga jika user tidak ditemukan

            $scanTime = Carbon::parse($attendance->scan_time)->format('H:i:s');
            
            // 1. Ambil aturan yang aktif
            $rules = PointRule::where('is_active', true)
                        ->whereIn('target_role', [$user->role, 'all'])
                        ->get();

            $totalPoints = 0;
            $descriptions =[];

            // 2. Evaluasi Mesin Aturan
            foreach ($rules as $rule) {
                $isMatch = false;

                if ($rule->condition_type == 'check_in_time') {
                    $isMatch = $this->evaluateTime($scanTime, $rule->condition_operator, $rule->condition_value);
                } elseif ($rule->condition_type == 'status') {
                    $isMatch = ($attendance->status === $rule->condition_value);
                }

                // Jika COCOK, hitung poinnya
                if ($isMatch) {
                    $modifier = (int) $rule->point_modifier; // Pastikan jadi angka
                    $totalPoints += $modifier;
                    
                    $simbol = $modifier > 0 ? '+' : '';
                    $descriptions[] = $rule->rule_name . " ({$simbol}{$modifier})";
                }
            }

            // 3. Eksekusi ke Database jika ada poin yang berubah
            if ($totalPoints != 0) {
                
                // PERBAIKAN: Hitung manual agar saldo selalu mutlak benar
                $saldoAwal = $user->integrity_points;
                $saldoAkhir = $saldoAwal + $totalPoints;
                
                // Opsional: Cegah poin minus (Jika poin < 0, jadikan 0)
                // Jika ingin poin bisa minus, hapus/komentar baris di bawah ini:
                if ($saldoAkhir < 0) {
                    $saldoAkhir = 0;
                }

                // Simpan ke tabel User
                $user->integrity_points = $saldoAkhir;
                $user->save();

                // Simpan ke Buku Besar (Ledger)
                $transType = $totalPoints > 0 ? 'EARN' : 'PENALTY';

                PointLedger::create([
                    'user_id' => $user->id,
                    'transaction_type' => $transType,
                    'amount' => abs($totalPoints), // Amount selalu positif
                    'current_balance' => $saldoAkhir,
                    'description' => implode(', ', $descriptions)
                ]);
                
                Log::info("Poin Gamifikasi - {$user->name} | Perubahan: {$totalPoints} | Saldo: {$saldoAkhir}");
            }
            
        } catch (\Exception $e) {
            Log::error("Observer Error: " . $e->getMessage());
        }
    }

    // Fungsi Pembantu Cek Waktu
    private function evaluateTime($scanTime, $operator, $ruleTime)
    {
        if (!$ruleTime) return false;
        
        $scan = strtotime($scanTime);
        $rule = strtotime($ruleTime);

        switch ($operator) {
            case '<': return $scan < $rule;
            case '>': return $scan > $rule;
            case '=': return $scan == $rule;
            case '<=': return $scan <= $rule;
            case '>=': return $scan >= $rule;
            default: return false;
        }
    }
}