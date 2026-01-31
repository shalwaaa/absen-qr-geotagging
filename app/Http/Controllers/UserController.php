<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'student');
        
        $search = $request->query('search');

        $query = User::where('role', $type);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nip_nis', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->with('classroom')->latest()->paginate(10);

        return view('admin.users.index', compact('users', 'type', 'search'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'student');
        $classrooms = Classroom::all(); 
        
        return view('admin.users.create', compact('type', 'classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users', 
            'role' => 'required|in:teacher,student',
            'nip_nis' => 'required|string|unique:users',
            'classroom_id' => 'nullable|required_if:role,student|exists:classrooms,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email, 
            'role' => $request->role,
            'nip_nis' => $request->nip_nis,
            'classroom_id' => $request->role == 'student' ? $request->classroom_id : null,
            'password' => Hash::make('smakzie123'),
            'is_piket' => $request->has('is_piket'), 
        ]);

        return redirect()->route('users.index', ['type' => $request->role])
                        ->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $classrooms = Classroom::all();
        return view('admin.users.edit', compact('user', 'classrooms'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'nip_nis' => ['required', Rule::unique('users')->ignore($user->id)],
            'classroom_id' => 'nullable|required_if:role,student|exists:classrooms,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip_nis' => $request->nip_nis,
            'classroom_id' => $user->role == 'student' ? $request->classroom_id : null,
            'is_piket' => $request->has('is_piket'),    
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index', ['type' => $user->role])
                         ->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $type = $user->role; 
        $user->delete();
        
        return redirect()->route('users.index', ['type' => $type])
                         ->with('success', 'Data berhasil dihapus!');
    }

        public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

}