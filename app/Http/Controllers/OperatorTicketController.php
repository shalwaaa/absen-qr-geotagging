<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OperatorTicketController extends Controller implements HasMiddleware
{
    // Keamanan: Hanya Admin & Operator yang boleh masuk
    public static function middleware(): array
    {
        return[
            new Middleware(function ($request, $next) {
                if (auth()->user()->role !== 'admin' && !auth()->user()->is_operator) {
                    abort(403, 'Akses khusus Operator Helpdesk.');
                }
                return $next($request);
            }),
        ];
    }

    // 1. Dashboard Antrean Tiket
    public function index(Request $request)
    {
        $status = $request->query('status', 'open'); // Default lihat tiket baru

        $tickets = Ticket::with(['user', 'operator'])
                         ->when($status, function($q) use ($status) {
                             if ($status != 'all') $q->where('status', $status);
                         })
                         // Urutkan berdasarkan prioritas dan waktu masuk
                         ->orderByRaw("FIELD(priority, 'high', 'mid', 'low')")
                         ->orderBy('created_at', 'asc')
                         ->paginate(15);

        return view('helpdesk.operator.index', compact('tickets', 'status'));
    }

    // 2. Lihat Detail Tiket (Sama dengan user, tapi ini operator)
    public function show($id)
    {
        $ticket = Ticket::with(['responses.user', 'user', 'operator'])->findOrFail($id);

        // Ambil saran balasan otomatis berdasarkan kategori tiket
        $suggestions = \App\Models\CannedResponse::where('category', $ticket->category)->get();

        return view('helpdesk.shared.show', compact('ticket', 'suggestions'));
    }

    // 3. Ambil Alih Tiket (Operator Assign to Self)
    public function takeTicket($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status == 'closed') {
            return back()->with('error', 'Tiket sudah ditutup.');
        }

        $ticket->update([
            'operator_id' => Auth::id(),
            'status' => 'in_progress'
        ]);

        return back()->with('success', 'Tiket berhasil diambil alih. Silakan mulai berikan solusi.');
    }

    // 4. Operator Balas Chat
    public function reply(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Jika ini balasan pertama operator, catat waktu respon (SLA)
        if ($ticket->status == 'open' && is_null($ticket->first_response_at)) {
            $ticket->update(['first_response_at' => now()]);
        }

        $request->validate(['message' => 'required|string']);
        $ticket = Ticket::findOrFail($id);

        TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        // Jika tiket masih open, otomatis jadi in progress karena sudah dibalas
        if ($ticket->status == 'open') {
            $ticket->update([
                'status' => 'in_progress',
                'operator_id' => Auth::id() // Otomatis assign
            ]);
        }

        return back()->with('success', 'Balasan terkirim ke pelapor.');
    }

    // 5. Tutup Tiket (Resolved)
    public function closeTicket($id) {
        $ticket = Ticket::findOrFail($id);
        // Catat waktu selesai (SLA)
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);
        
        \App\Models\TicketResponse::create([
        'ticket_id' => $ticket->id,
        'user_id' => Auth::id(), // Operator yang menutup tiket
        'message' => 'Sistem: Masalah Anda telah ditangani oleh operator. Tiket ini resmi ditutup. Terima kasih.',
        'is_auto_reply' => true
    ]);

    return back()->with('success', 'Tiket berhasil diselesaikan.');
    }
}
