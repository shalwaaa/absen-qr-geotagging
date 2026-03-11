<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\AcademicYear;
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
        // Ambil Filter
        $type = $request->query('type', 'student');
        $search = $request->query('search');
        $yearId = $request->query('year_id'); // Filter Tahun

        // Ambil Tahun Aktif sebagai default jika tidak ada filter
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$yearId && $activeYear) {
            $yearId = $activeYear->id;
        }

        // QUERY UTAMA
        $query = User::where('role', $type);

        if ($type == 'student' && $yearId) {
            // FILTER ALA FOLDER
            // Ambil siswa yang PUNYA DATA di tabel class_members pada tahun yg dipilih
            $query->whereHas('classMembers', function($q) use ($yearId) {
                $q->where('academic_year_id', $yearId);
            });
            
            // Eager Load data kelas pada tahun tersebut
            $query->with(['classMembers' => function($q) use ($yearId) {
                $q->where('academic_year_id', $yearId)->with('classroom');
            }]);
        }

        // Filter berdasarkan nama atau NIP/NIS
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nip_nis', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate(10);
        $years = AcademicYear::orderBy('start_date', 'desc')->get(); // Untuk dropdown filter

        return view('admin.users.index', compact('users', 'type', 'search', 'years', 'yearId'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'student');
        $classrooms = Classroom::all(); 
        
        // TAMBAHAN: Cek apakah sudah ada Kepala Sekolah?
        $existingHeadmaster = User::where('is_headmaster', true)->first();
        
        return view('admin.users.create', compact('type', 'classrooms', 'existingHeadmaster'));
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
            'is_headmaster' => $request->has('is_headmaster'),
        ]);

        return redirect()->route('users.index', ['type' => $request->role])
                        ->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $classrooms = Classroom::all();
        
        // Cek apakah ada user lain yang sudah jadi headmaster
        $existingHeadmaster = User::where('is_headmaster', true)
                                  ->where('id', '!=', $user->id) // Kecuali user yang sedang diedit
                                  ->first();

        

        return view('admin.users.edit', compact('user', 'classrooms', 'existingHeadmaster'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'nip_nis' => ['required', Rule::unique('users')->ignore($user->id)],
            'classroom_id' => 'nullable|required_if:role,student|exists:classrooms,id',
        ]);

        if ($request->has('is_headmaster') && $request->is_headmaster == '1') {
            User::where('is_headmaster', true)->update(['is_headmaster' => false]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip_nis' => $request->nip_nis,
            'classroom_id' => $user->role == 'student' ? $request->classroom_id : null,
            'is_piket' => $request->has('is_piket'), // PASTIKAN INI ADA
            'is_headmaster' => $request->has('is_headmaster'),
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

    // Fungsi Hapus Massal
    public function deleteAll(Request $request)
    {
        $type = $request->input('type'); // 'teacher' atau 'student'

        if (!in_array($type, ['teacher', 'student'])) {
            return back()->with('error', 'Tipe user tidak valid.');
        }

        // Hapus semua user dengan role tersebut (akan cascade jika migration sudah diatur)
        User::where('role', $type)->delete();

        return back()->with('success', 'Semua data ' . ($type == 'teacher' ? 'Guru' : 'Siswa') . ' berhasil dihapus.');
    }

}