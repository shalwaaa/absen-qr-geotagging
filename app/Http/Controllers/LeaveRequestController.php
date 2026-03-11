<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index()
    {
        // List izin saya
        $requests = LeaveRequest::where('student_id', Auth::id())->latest()->paginate(10);
        return view('student.leaves.index', compact('requests'));
    }

    public function create()
    {
        // Tampilkan form pengajuan izin
        return view('student.leaves.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'type' => 'required|in:sick,permission',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|image|max:2048',
        ]);

        // Handle file upload jika ada
        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }

        // Logic Guru vs Siswa
        $user = Auth::user();
        
        LeaveRequest::create([
            'student_id' => $user->id, // User ID (bisa guru/siswa)
            'classroom_id' => $user->role == 'student' ? $user->classroom_id : null, // Guru = Null
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'attachment' => $path,
            'status' => 'pending'
        ]);

        return redirect()->route('leaves.index')->with('success', 'Pengajuan berhasil dikirim.');
    }
}