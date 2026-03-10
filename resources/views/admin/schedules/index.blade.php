<x-app-layout>
    <style>
        /* TAB FOLDER (TINGKAT) */
        .grade-tabs {
            display: flex; gap: 8px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 4px;
        }
        .grade-tab {
            padding: 10px 24px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
            display: flex; align-items: center; gap: 8px;
            white-space: nowrap;
        }
        .grade-tab:hover { background: #f8fafc; color: #1e293b; }
        .grade-tab.active {
            background: #4a6741; color: white; border-color: #4a6741;
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2);
        }

        /* Search Bar */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 16px; flex-wrap: wrap; }
        .search-box { position: relative; flex: 1; max-width: 400px; }
        .search-input { width: 100%; padding-left: 40px; border-radius: 12px; border: 1px solid #e2e8f0; height: 48px; outline: none; font-size: 14px; }
        .search-input:focus { border-color: #4a6741; box-shadow: 0 0 0 3px rgba(74, 103, 65, 0.1); }
        .search-icon { position: absolute; left: 14px; top: 14px; color: #94a3b8; font-size: 16px; }

        /* Komponen utama */
        .custom-card { border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; background: white; }
        
        .btn-primary { background: #4a6741; color: white; border-radius: 12px; font-weight: 600; padding: 0 24px; height: 48px; font-size: 14px; border: none; display: flex; align-items: center; gap: 8px; text-decoration: none; transition: 0.2s; }
        .btn-primary:hover { background: #3d5535; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2); }
        
        .modern-table { width: 100%; border-collapse: collapse; }
        .modern-table th { color: #64748b; font-weight: 700; text-transform: uppercase; padding: 16px; font-size: 11px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .modern-table td { padding: 16px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        
        .classroom-badge { display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #1e40af; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; border: 1px solid #dbeafe; }
        
        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; background: white; margin-right: 4px; transition: 0.2s; color: #64748b; }
        .btn-view:hover { background: #e0f2fe; border-color: #7dd3fc; color: #0284c7; }
        .btn-edit:hover { background: #fef3c7; border-color: #fcd34d; color: #d97706; }

        /* Pagination custom */
        .custom-pagination { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-top: 1px solid #f1f5f9; background-color: white; flex-wrap: wrap; gap: 12px; }
        .pagination-info { font-size: 13px; color: #64748b; font-weight: 500; }
        .pagination-links { display: flex; gap: 6px; }
        .page-link { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 6px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #475569; background: white; border: 1px solid #e2e8f0; text-decoration: none; transition: all 0.2s; }
        .page-link:hover:not(.disabled) { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }
        .page-link.active { background: #4a6741; color: white; border-color: #4a6741; box-shadow: 0 2px 4px rgba(74, 103, 65, 0.2); }
        .page-link.disabled { color: #cbd5e1; cursor: not-allowed; background: #f8fafc; }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Manajemen Jadwal Pelajaran</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <!-- TOOLBAR: SEARCH & TAMBAH JADWAL -->
            <div class="toolbar">
                <form method="GET" action="{{ route('schedules.index') }}" class="search-box">
                    @if($grade) <input type="hidden" name="grade" value="{{ $grade }}"> @endif
                    
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kelas..." class="search-input">
                </form>

                <a href="{{ route('schedules.create') }}" class="btn-primary shadow-sm">
                    <i class="fa-solid fa-calendar-plus text-xs"></i>
                    <span>Buat Jadwal Baru</span>
                </a>
            </div>

            <!-- FILTER TINGKAT KELAS -->
            <div class="grade-tabs">
                <a href="{{ route('schedules.index', ['search' => $search]) }}" 
                   class="grade-tab {{ !$grade ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Semua Tingkat
                </a>
                @foreach([10, 11, 12] as $g)
                    <a href="{{ route('schedules.index', ['grade' => $g, 'search' => $search]) }}" 
                       class="grade-tab {{ $grade == $g ? 'active' : '' }}">
                        <i class="fa-solid fa-folder"></i> Kelas {{ $g }}
                    </a>
                @endforeach
            </div>

            <!-- DAFTAR KELAS (BUKAN DAFTAR JADWAL) -->
            <div class="custom-card">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">No</th>
                                <th>Nama Kelas</th>
                                <th class="text-center" width="150">Tingkat</th>
                                <th class="text-center" width="150">Jadwal Aktif</th>
                                <th class="text-center" width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classrooms as $index => $class)
                            <tr>
                                <td class="text-center text-gray-400 font-mono">
                                    {{ $index + 1 + ($classrooms->currentPage() - 1) * $classrooms->perPage() }}
                                </td>
                                
                                <!-- Nama Kelas -->
                                <td>
                                    <span class="font-bold text-slate-800 text-base">{{ $class->name }}</span>
                                </td>

                                <!-- Tingkat -->
                                <td class="text-center">
                                    <span class="classroom-badge">Kelas {{ $class->grade_level }}</span>
                                </td>

                                <!-- Jumlah Jadwal (Opsional, perlu withCount di controller) -->
                                <td class="text-center">
                                    <!-- Sementara kosong atau hitung manual jika ada relasi -->
                                    <span class="text-xs text-gray-500 font-bold bg-gray-100 px-2 py-1 rounded">
                                        Lihat Detail
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="flex justify-center gap-2">
                                        <!-- Tombol Lihat Jadwal (Mata) -->
                                        <a href="{{ route('schedules.classroom.show', $class->id) }}" class="btn-action btn-view" title="Lihat Jadwal Kelas Ini">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        
                                        <!-- Tombol Edit Jadwal (Pensil) - Mengarah ke Create dengan pre-filled class_id -->
                                        <a href="{{ route('schedules.create', ['classroom_id' => $class->id]) }}" class="btn-action btn-primary" title="Tambah Jadwal di Kelas Ini">
                                            <i class="fa-solid fa-plus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-slate-400">
                                    <i class="fa-regular fa-building text-4xl mb-3 block opacity-20"></i>
                                    <p>Tidak ada kelas yang ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                @if($classrooms->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-info">
                        Menampilkan {{ $classrooms->firstItem() }} - {{ $classrooms->lastItem() }} dari {{ $classrooms->total() }} kelas
                    </div>
                    <div class="pagination-links">
                        @if ($classrooms->onFirstPage())
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $classrooms->appends(['grade' => $grade, 'search' => $search])->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
                        @endif

                        @foreach ($classrooms->getUrlRange(max(1, $classrooms->currentPage() - 2), min($classrooms->lastPage(), $classrooms->currentPage() + 2)) as $page => $url)
                            @if ($page == $classrooms->currentPage())
                                <span class="page-link active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&grade={{$grade}}&search={{$search}}" class="page-link">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($classrooms->hasMorePages())
                            <a href="{{ $classrooms->appends(['grade' => $grade, 'search' => $search])->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
                        @else
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>