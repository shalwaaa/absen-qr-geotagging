<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeadmasterController extends Controller
{
    // 1. Tampilkan Halaman Pengajuan Izin Guru
    public function index()
    {
        $user = Auth::user();

        // Izinkan masuk jika: (Role = Teacher DAN is_headmaster = 1) ATAU (Role = Admin)
        if (!($user->role == 'teacher' && $user->is_headmaster) && $user->role !== 'admin') {
            abort(403, 'Akses Ditolak. Halaman ini khusus Kepala Sekolah.');
        }

        // Ambil pengajuan izin HANYA dari user yang role-nya 'teacher'
        $query = LeaveRequest::whereHas('student', function($q) {
            $q->where('role', 'teacher');
        });

        // 1. Pending
        $pendingLeaves = (clone $query)->where('status', 'pending')
                                       ->with('student')
                                       ->orderBy('created_at', 'asc')
                                       ->get();

        // 2. Riwayat (Pagination 15)
        $historyLeaves = (clone $query)->whereIn('status', ['approved', 'rejected'])
                                       ->with('student')
                                       ->orderBy('updated_at', 'desc')
                                       ->paginate(15);

        return view('admin.headmaster.index', compact('pendingLeaves', 'historyLeaves'));
    }

    // 2. Proses Persetujuan / Penolakan Pengajuan Izin Guru
    public function updateStatus(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        
        if ($request->action == 'approve') {
            $leave->status = 'approved';
        } else {
            $leave->status = 'rejected';
        }

        $leave->notes = $request->notes;
        $leave->save();

        return back()->with('success', 'Status pengajuan guru diperbarui.');
    }
}