<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointRule;
use App\Models\FlexibilityItem;
use App\Models\User;

class GamificationAdminController extends Controller
{
    public function index()
    {
        // 1. Ambil data Rules
        $rules = PointRule::latest()->get();

        // 2. Ambil data Item Marketplace
        $items = FlexibilityItem::latest()->get();

        // 3. Ambil Leaderboard (Top 10 Siswa dengan Poin Tertinggi)
        $leaderboard = User::where('role', 'student')
                           ->orderBy('integrity_points', 'desc')
                           ->take(10)
                           ->get();

        return view('admin.gamification.index', compact('rules', 'items', 'leaderboard'));
    }

    // --- MANAJEMEN RULES ---
    public function storeRule(Request $request)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'target_role' => 'required|in:student,teacher,all',
            'condition_type' => 'required|string',
            'condition_operator' => 'nullable|in:<,>,=,<=,>=',
            'condition_value' => 'required|string',
            'point_modifier' => 'required|integer', 
        ]);

        PointRule::create($request->all());

        return back()->with('success', 'Aturan (Rule) baru berhasil ditambahkan.');
    }

    public function destroyRule($id)
    {
        PointRule::findOrFail($id)->delete();
        return back()->with('success', 'Aturan berhasil dihapus.');
    }

    // --- MANAJEMEN MARKETPLACE ---
    public function storeItem(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_type' => 'required|in:late_pass,permission_pass,custom',
            'point_cost' => 'required|integer|min:1',
            'value_minutes' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        // Mapping Icon berdasarkan tipe
        $icon = 'fa-ticket';
        if ($request->item_type == 'late_pass') $icon = 'fa-stopwatch';
        if ($request->item_type == 'permission_pass') $icon = 'fa-envelope-open-text';

        FlexibilityItem::create([
            'item_name' => $request->item_name,
            'item_type' => $request->item_type,
            'point_cost' => $request->point_cost,
            'value_minutes' => $request->value_minutes,
            'description' => $request->description,
            'icon' => $icon,
            'is_active' => true
        ]);

        return back()->with('success', 'Item Reward berhasil ditambahkan ke Marketplace.');
    }

    public function destroyItem($id)
    {
        FlexibilityItem::findOrFail($id)->delete();
        return back()->with('success', 'Item berhasil dihapus dari Marketplace.');
    }
}