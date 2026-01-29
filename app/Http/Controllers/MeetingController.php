<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MeetingController extends Controller
{
            
    public function index()
    {

        Carbon::setLocale('id');
        $today = Carbon::now()->isoFormat('dddd'); 

        $schedules = Schedule::with(['subject', 'classroom'])
            ->where('teacher_id', Auth::id())
            ->where('day', $today)
            ->get();

        foreach ($schedules as $s) {
            $s->today_meeting = Meeting::where('schedule_id', $s->id)
                                       ->whereDate('date', Carbon::today())
                                       ->first();
        }

        return view('teacher.dashboard', compact('schedules', 'today'));
    }

    // (Generate QR)
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $existing = Meeting::where('schedule_id', $request->schedule_id)
                           ->whereDate('date', Carbon::today())
                           ->first();

        if ($existing) {
            return redirect()->route('meetings.show', $existing->id);
        }

        $meeting = Meeting::create([
            'schedule_id' => $request->schedule_id,
            'date' => Carbon::today(),
            'qr_token' => Str::random(40),
            'is_active' => true,
        ]);

        return redirect()->route('meetings.show', $meeting->id);
    }

    // Halaman Tampil QR Code
public function show($id)
    {
        $meeting = Meeting::with(['schedule.subject', 'schedule.classroom', 'attendances.student'])
                          ->findOrFail($id);
        
        
        $user = Auth::user();
        
        if ($meeting->schedule->teacher_id != $user->id && $user->role != 'admin') {
            abort(403, 'Anda bukan pengajar di kelas ini.');
        }

        return view('teacher.meeting_show', compact('meeting'));
    }

    public function toggleStatus($id)
    {
        $meeting = Meeting::findOrFail($id);

        $meeting->is_active = !$meeting->is_active;
        $meeting->save();

        return redirect()->back();
    }
}