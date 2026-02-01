<x-app-layout>
    <style>
        /* 1. Animasi */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-standard { animation: fadeIn 0.4s ease-out forwards; }

        /* 2. Card & Header */
        .custom-card {
            background: white; border-radius: 16px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;
        }

        /* 3. Search & Filter Bar */
        .toolbar {
            display: flex; gap: 12px; align-items: center; 
            margin-bottom: 24px; flex-wrap: wrap;
        }
        
        .search-box {
            position: relative; flex: 1; min-width: 200px;
        }
        .search-input {
            width: 100%; padding: 10px 10px 10px 40px;
            border-radius: 10px; border: 1px solid #e2e8f0;
            font-size: 14px; outline: none; transition: 0.2s;
        }
        .search-input:focus { border-color: #4a6741; box-shadow: 0 0 0 3px rgba(74, 103, 65, 0.1); }
        .search-icon { position: absolute; left: 14px; top: 13px; color: #94a3b8; }

        .filter-select {
            padding: 10px 30px 10px 14px; border-radius: 10px; border: 1px solid #e2e8f0;
            font-size: 14px; outline: none; cursor: pointer; color: #475569;
            background: white; transition: 0.2s; height: 42px;
        }
        .filter-select:focus { border-color: #4a6741; }

        /* 4. Buttons */
        .btn-primary {
            background: #4a6741; color: white; border-radius: 10px;
            padding: 10px 20px; font-weight: 600; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px;
            transition: 0.2s; border: none; cursor: pointer; text-decoration: none;
        }
        .btn-primary:hover { background: #3d5535; transform: translateY(-1px); }

        .import-form { display: flex; align-items: center; gap: 8px; background: white; padding: 4px; border: 1px solid #e2e8f0; border-radius: 10px; }
        .file-input { font-size: 12px; width: 180px; }

        /* 5. Table */
        .modern-table { width: 100%; border-collapse: collapse; }
        .modern-table th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 11px; padding: 14px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .modern-table td { padding: 14px 16px; color: #334155; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        .modern-table tr:hover { background: #fcfdfa; }

        /* Badges */
        .badge-id { background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 12px; }
        .badge-class { background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 11px; }
        .badge-piket { background: #ffedd5; color: #9a3412; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; border: 1px solid #fed7aa; margin-left: 8px; text-transform: uppercase; }

        /* Actions */
        .action-btn { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; transition: 0.2s; background: white; color: #64748b; }
        .action-btn:hover { border-color: #cbd5e1; color: #334155; transform: scale(1.05); }
        .text-red:hover { color: #ef4444; border-color: #fca5a5; background: #fef2f2; }
        .text-orange:hover { color: #f59e0b; border-color: #fcd34d; background: #fffbeb; }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800">
                <span style="color: #E4EB9C;">Manajemen Data:</span> 
                <span class="text-[#2D5128]">{{ $type == 'teacher' ? 'Guru' : 'Siswa' }}</span>
            </h2>
            <span class="bg-[#f0fdf4] text-[#166534] text-xs font-bold px-3 py-1 rounded-full border border-[#bbf7d0]">
                Total: {{ $users->total() }} Data
            </span>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-standard">
        <div class="max-w-7xl mx-auto">

            <!-- TOOLBAR: SEARCH, FILTER, IMPORT, ADD -->
            <div class="toolbar">
                
                <!-- 1. SEARCH -->
                <form method="GET" action="{{ route('users.index') }}" class="search-box">
                    <input type="hidden" name="type" value="{{ $type }}">
                    @if(isset($yearId)) <input type="hidden" name="year_id" value="{{ $yearId }}"> @endif
                    
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari nama atau nomor induk..." 
                           class="search-input" onchange="this.form.submit()">
                </form>

                <!-- 2. FILTER TAHUN (HANYA MUNCUL DI DATA SISWA) -->
                @if($type == 'student')
                    <form method="GET" action="{{ route('users.index') }}">
                        <input type="hidden" name="type" value="student">
                        @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                        
                        <div class="flex items-center gap-2">
                            <select name="year_id" onchange="this.form.submit()" class="filter-select" title="Pilih Folder Tahun">
                                <option value="">-- Semua Data --</option>
                                @foreach($years as $y)
                                    <option value="{{ $y->id }}" {{ $yearId == $y->id ? 'selected' : '' }}>
                                        📂 {{ $y->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                @endif

                <div class="flex-1"></div> <!-- Spacer -->

                <!-- 3. IMPORT EXCEL -->
                <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="import-form">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="file" name="file" class="file-input" required>
                    <button type="submit" class="text-xs font-bold text-[#4a6741] px-3 py-2 hover:bg-gray-50 rounded">
                        <i class="fa-solid fa-file-import"></i> Import
                    </button>
                </form>

                <!-- 4. TAMBAH MANUAL -->
                <a href="{{ route('users.create', ['type' => $type]) }}" class="btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>

            <!-- TABLE -->
            <div class="custom-card">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Lengkap</th>
                                <th width="150" class="text-center">{{ $type == 'teacher' ? 'NIP' : 'NIS' }}</th>
                                
                                @if($type == 'student') 
                                    <th width="200" class="text-center">Kelas (History)</th> 
                                @endif
                                
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $u)
                            <tr>
                                <td class="text-center text-gray-400 font-mono">
                                    {{ $index + 1 + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>
                                
                                <!-- Nama -->
                                <td>
                                    <span class="font-bold text-gray-800">{{ $u->name }}</span>
                                    @if($u->is_piket)
                                        <span class="badge-piket">PIKET</span>
                                    @endif
                                </td>

                                <!-- NIP/NIS -->
                                <td class="text-center">
                                    <span class="badge-id">{{ $u->nip_nis ?? '-' }}</span>
                                </td>

                                <!-- Logic Kelas History (Khusus Siswa) -->
                                @if($type == 'student')
                                    <td class="text-center">
                                        @php
                                            // Cari data kelas di tahun yang dipilih
                                            $history = null;
                                            if($yearId && $u->classMembers) {
                                                $history = $u->classMembers->where('academic_year_id', $yearId)->first();
                                            }
                                        @endphp

                                        @if($history)
                                            <span class="badge-class">{{ $history->classroom->name }}</span>
                                        @elseif($u->classroom) 
                                            <!-- Fallback: Tampilkan kelas master jika history belum ada -->
                                            <span class="badge-class bg-gray-100 text-gray-500">{{ $u->classroom->name }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endif

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('users.edit', ['user' => $u->id, 'type' => $type]) }}" class="action-btn text-orange" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="type" value="{{ $type }}">
                                            <button type="submit" class="action-btn text-red" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $type == 'student' ? 5 : 4 }}" class="text-center py-16 text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-regular fa-folder-open text-4xl mb-2 text-gray-300"></i>
                                        <p>Data tidak ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        {{ $users->appends(['type' => $type, 'search' => $search, 'year_id' => $yearId])->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>