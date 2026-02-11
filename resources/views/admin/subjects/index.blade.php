<x-app-layout>
    <style>
        /* (Style lama kamu tetap dipakai, saya tambah style filter) */
        .grade-tabs {
            display: flex; gap: 8px; margin-bottom: 20px;
        }
        .grade-tab {
            padding: 10px 20px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
            display: flex; align-items: center; gap: 6px;
        }
        .grade-tab:hover { background: #f8fafc; color: #1e293b; }
        
        .grade-tab.active {
            background: #4a6741;
            color: white;
            border-color: #4a6741;
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2);
        }

        /* Badge Tingkat */
        .badge-grade {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 6px;
            background: #eef2ff;
            color: #4338ca;
            font-weight: 700;
        }

        /* Search Bar Style */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 16px; flex-wrap: wrap; }
        .search-box { position: relative; flex: 1; max-width: 300px; }
        .search-input { width: 100%; padding-left: 36px; border-radius: 10px; border: 1px solid #e2e8f0; height: 42px; outline: none; }
        .search-input:focus { border-color: #4a6741; }
        .search-icon { position: absolute; left: 12px; top: 12px; color: #94a3b8; }
        
        /* ... Sisa style lama ... */
        .modern-table { width: 100%; border-collapse: collapse; }
        .modern-table th { background: #f8fafc; color: #64748b; padding: 16px; text-align: left; font-size: 11px; font-weight: 600; uppercase; border-bottom: 1px solid #e2e8f0; }
        .modern-table td { padding: 16px; color: #334155; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .subject-code { display: inline-block; padding: 2px 8px; background: #f1f5f9; color: #475569; font-family: monospace; font-size: 12px; border-radius: 6px; border: 1px solid #e2e8f0; font-weight: 600; }
        .btn-action { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; background: white; margin-right: 4px; }
        .btn-edit:hover { background: #fef3c7; border-color: #fbbf24; color: #d97706; }
        .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
        .btn-primary { background: #4a6741; color: white; border-radius: 10px; padding: 10px 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .custom-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Data Mata Pelajaran</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- TOOLBAR -->
            <div class="toolbar">
                <!-- SEARCH -->
                <form method="GET" action="{{ route('subjects.index') }}" class="search-box">
                    @if($grade) <input type="hidden" name="grade" value="{{ $grade }}"> @endif
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari mapel..." class="search-input">
                </form>

                <a href="{{ route('subjects.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Mapel
                </a>
            </div>

            <!-- FOLDER TABS (TINGKAT) -->
            <div class="grade-tabs">
                <a href="{{ route('subjects.index') }}" class="grade-tab {{ !$grade ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Semua
                </a>
                <a href="{{ route('subjects.index', ['grade' => 10]) }}" class="grade-tab {{ $grade == 10 ? 'active' : '' }}">
                    <i class="fa-solid fa-folder"></i> Kelas 10
                </a>
                <a href="{{ route('subjects.index', ['grade' => 11]) }}" class="grade-tab {{ $grade == 11 ? 'active' : '' }}">
                    <i class="fa-solid fa-folder"></i> Kelas 11
                </a>
                <a href="{{ route('subjects.index', ['grade' => 12]) }}" class="grade-tab {{ $grade == 12 ? 'active' : '' }}">
                    <i class="fa-solid fa-folder"></i> Kelas 12
                </a>
            </div>

            <div class="custom-card">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">No</th>
                                <th class="text-center" width="120">Tingkat</th>
                                <th class="text-center" width="150">Kode</th>
                                <th>Nama Mata Pelajaran</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjects as $index => $s)
                            <tr>
                                <td class="text-center text-gray-400 font-mono">
                                    {{ $index + 1 + ($subjects->currentPage() - 1) * $subjects->perPage() }}
                                </td>
                                <td class="text-center">
                                    <span class="badge-grade">Kelas {{ $s->grade_level }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="subject-code">{{ $s->code ?? '-' }}</span>
                                </td>
                                <td class="font-bold text-slate-700">{{ $s->name }}</td>
                                <td class="text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('subjects.edit', $s->id) }}" class="btn-action btn-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <form action="{{ route('subjects.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus mapel ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2"></i>
                                    <p>Tidak ada data mapel di folder ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($subjects->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $subjects->appends(['grade' => $grade, 'search' => $search])->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>