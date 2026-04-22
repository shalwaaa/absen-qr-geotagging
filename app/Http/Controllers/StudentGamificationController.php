<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlexibilityItem;
use App\Models\PointLedger;
use App\Models\UserToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentGamificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Riwayat Mutasi (Ledger)
        $ledgers = $user->ledgers()->latest()->get();

        // 2. Ambil Katalog Marketplace yang Aktif
        $items = FlexibilityItem::where('is_active', true)->latest()->get();

        // 3. Ambil Inventory Tas Siswa (Token yang dibeli)
        $tokens = $user->tokens()->with('item')->latest()->get();

        // 4. Hitung Level (Gamifikasi Visual)
        $level = 'Newbie';
        $badge = 'fa-medal text-gray-400';
        if ($user->integrity_points >= 50) { $level = 'Disiplin Bronze'; $badge = 'fa-medal text-orange-400'; }
        if ($user->integrity_points >= 100) { $level = 'Disiplin Silver'; $badge = 'fa-medal text-gray-300'; }
        if ($user->integrity_points >= 200) { $level = 'Disiplin Gold'; $badge = 'fa-medal text-yellow-400'; }
        if ($user->integrity_points >= 500) { $level = 'Disiplin Elite'; $badge = 'fa-crown text-yellow-500'; }

        return view('student.gamification.index', compact('user', 'ledgers', 'items', 'tokens', 'level', 'badge'));
    }

    public function purchase(Request $request, $id)
    {
        $user = Auth::user();
        $item = FlexibilityItem::findOrFail($id);

        // Validasi Saldo: Cukup nggak poinnya?
        if ($user->integrity_points < $item->point_cost) {
            return back()->with('error', 'Poin Integritas tidak mencukupi untuk menukar item ini.');
        }

        try {
            DB::transaction(function () use ($user, $item) {
                // 1. Potong Saldo Poin Siswa
                $user->decrement('integrity_points', $item->point_cost);

                // 2. Catat Mutasi ke Buku Besar (Ledger)
                PointLedger::create([
                    'user_id' => $user->id,
                    'transaction_type' => 'SPEND',
                    'amount' => $item->point_cost,
                    'current_balance' => $user->fresh()->integrity_points,
                    'description' => "Menukar poin dengan: " . $item->item_name
                ]);

                // 3. Masukkan Item ke dalam Tas Siswa (Inventory)
                UserToken::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'status' => 'AVAILABLE'
                ]);
            });

            return back()->with('success', 'Berhasil menukar poin dengan ' . $item->item_name . '! Cek tab Inventory Anda.');

        } catch (\Exception $e) {
            return back()->with('error', 'Transaksi gagal: ' . $e->getMessage());
        }
    }
}