<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return view('admin.dashboard');
        } 
        
        if ($role == 'teacher') {
            return view('teacher.dashboard');
        } 
        
        if ($role == 'student') {
            return view('student.dashboard');
        }

        return abort(403);
    }
}