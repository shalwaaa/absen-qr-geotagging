<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\SatisfactionRating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminHelpdeskController extends Controller
{
    public function index() {
    // 1. Data Kotak Statistik (Poin Tambahan)
    $countOpen = Ticket::where('status', 'open')->count();
    $countProgress = Ticket::where('status', 'in_progress')->count();
    $countClosed = Ticket::where('status', 'closed')->count();
    $countTotal = Ticket::count();

    // 2. Data SLA & Rating (Poin 4 Tugas)
    $avgResponse = Ticket::whereNotNull('first_response_at')
        ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as minutes')
        ->first()->minutes ?? 0;
    $avgRating = \App\Models\SatisfactionRating::avg('score') ?? 0;

    // 3. Data Pie Chart (Distribusi Kategori)
    $categoryStats = Ticket::select('category', \DB::raw('count(*) as count'))
                           ->groupBy('category')->get();

    // 4. Data Performa Operator
    $operatorStats = \App\Models\User::where('role', 'admin') // atau role operator khusus
        ->withCount(['operatorTickets as resolved_count' => function($q) {
            $q->where('status', 'closed');
        }])->get();

    return view('helpdesk.admin.analytics', compact(
        'countOpen', 'countProgress', 'countClosed', 'countTotal',
        'avgResponse', 'avgRating', 'categoryStats', 'operatorStats'
    ));
}
}
