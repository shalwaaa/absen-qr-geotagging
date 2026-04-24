<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

//Enc
class UserTicketController extends Controller
//Inh
{
    // 1. Menampilkan Daftar Tiket Saya
    public function index()
    {
        //Pol
        $tickets = Ticket::where('user_id', Auth::id())
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
        return view('helpdesk.user.index', compact('tickets'));
    }

    // 2. Form Untuk Tiket Baru
    public function create()
    {
        return view('helpdesk.user.create');
    }

    // 3. Simpan Tiket Baru
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,mid,high'
        ]);

        //ABS, Generate Kode Tiket Unik (Contoh: TKT-20260423-ABCDE)
        $ticketCode = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $ticket = Ticket::create([
            'ticket_code' => $ticketCode,
            'user_id' => Auth::id(),
            'category' => $request->category,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'open'
        ]);

            \App\Models\TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => 1, // ID Admin atau Bot (Pastikan ID ini ada di tabel users)
            'message' => 'Halo! Laporan Anda telah kami terima. Tim Operator akan segera meninjau kendala Anda. Mohon ditunggu ya.',
            'is_auto_reply' => true
        ]);


        return redirect()->route('user.tickets.show', $ticket->id)
                         ->with('success', 'Tiket aduan berhasil dibuat! Tim kami akan segera merespon.');
    }

    // 4. Lihat Detail & Chat Room Tiket
    public function show($id)
    {
        //pol
        $ticket = Ticket::where('id', $id)
                        ->where('user_id', Auth::id()) // Keamanan: Hanya boleh lihat tiketnya sendiri
                        ->with(['responses.user', 'operator'])
                        ->firstOrFail();

        return view('helpdesk.shared.show', compact('ticket'));
    }

    // 5. Pelapor Balas Chat
    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        $ticket = Ticket::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($ticket->status == 'closed') {
            return back()->with('error', 'Tiket sudah ditutup, tidak dapat membalas pesan.');
        }

        TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }

    //anti duplikasi tiket dengan fitur cerdas: search similar tickets
      public function searchSimilar(Request $request)
    {
        $keyword = $request->query('q');

        if (!$keyword || strlen($keyword) < 4) {
            return response()->json([]);
        }

        // Menggunakan Full-Text Search pada kolom subject dan description
        $tickets = Ticket::whereRaw("MATCH(subject, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$keyword])
                         ->where('status', '!=', 'closed') // Tampilkan yang masih open/in progress
                         ->select('id', 'ticket_code', 'subject', 'status')
                         ->take(3)
                         ->get();

        return response()->json($tickets);
    }

    public function rate(Request $request, $id)
{
    $request->validate([
        'score' => 'required|integer|min:1|max:5',
        'feedback' => 'nullable|string'
    ]);

    // Pastikan tiket milik user yang login dan statusnya sudah closed
    $ticket = \App\Models\Ticket::where('id', $id)
                ->where('user_id', auth()->id())
                ->where('status', 'closed')
                ->firstOrFail();

    \App\Models\SatisfactionRating::updateOrCreate(
        ['ticket_id' => $id],
        [
            'score' => $request->score,
            'feedback' => $request->feedback
        ]
    );

    return back()->with('success', 'Terima kasih atas penilaian Anda!');
}
}
