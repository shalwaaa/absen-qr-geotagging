<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {

        $classrooms = Classroom::orderBy('grade_level', 'asc')
                               ->orderBy('name', 'asc')
                               ->get();
        return view('admin.promotions.index', compact('classrooms'));
    }

    public function getStudents($classroomId)
    {
        $students = User::where('classroom_id', $classroomId)
                        ->where('role', 'student')
                        ->where('status', 'active') 
                        ->orderBy('name')
                        ->get(['id', 'name', 'nip_nis']); 
        
        return response()->json($students);
    }

    public function process(Request $request)
    {
        $request->validate([
            'from_classroom_id' => 'required',
            'student_ids' => 'required|array', 
            'action' => 'required', 
            'to_classroom_id' => 'required_if:action,promote',
        ]);

        DB::transaction(function () use ($request) {
            
            if ($request->action == 'promote') {
                User::whereIn('id', $request->student_ids)
                    ->update(['classroom_id' => $request->to_classroom_id]);
            }

            elseif ($request->action == 'graduate') {
                User::whereIn('id', $request->student_ids)
                    ->update([
                        'classroom_id' => null, 
                        'status' => 'graduated'
                    ]);
            }
        });

        return back()->with('success', count($request->student_ids) . ' Siswa berhasil d iproses!');
    }
}