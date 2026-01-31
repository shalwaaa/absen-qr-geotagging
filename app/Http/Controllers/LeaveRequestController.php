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
        $requests = LeaveRequest::where('student_id', Auth::id())->latest()->get();
        return view('student.leaves.index', compact('requests'));
    }

    public function create()
    {
        return view('student.leaves.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sick,permission',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|image|max:2048', // Bukti foto (opsional/wajib)
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }

        LeaveRequest::create([
            'student_id' => Auth::id(),
            'classroom_id' => Auth::user()->classroom_id, // Mengambil kelas saat ini
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'attachment' => $path,
            'status' => 'pending'
        ]);

        return redirect()->route('leaves.index')->with('success', 'Pengajuan berhasil dikirim ke Wali Kelas.');
    }
}