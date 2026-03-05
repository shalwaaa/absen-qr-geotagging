<x-app-layout>
    <style>
        /* 1. Animasi Standar */
        @keyframes fadeInStandard { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-standard { animation: fadeInStandard 0.4s ease-out forwards; }

        /* 2. Card & Header */
        .custom-card { border-radius: 16px !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0 !important; overflow: hidden; background: white; }

        /* 3. TOOLBAR SEIMBANG (SEARCH & BUTTON) */
        .toolbar-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }

        /* Search Styles */
        .search-form {
            position: relative;
            width: 100%;
        }
        
        .search-input {
            width: 100%;
            height: 48px; /* Tinggi fix biar sama dengan tombol */
            padding: 0 16px 0 44px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            background: white;
            color: #334155;
        }
        
        .search-input:focus {
            border-color: #4a6741;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }

        /* Button Styles */
        .btn-primary {
            background: #4a6741;
            color: white;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 24px;
            height: 48px; /* Tinggi disamakan dengan input */
            font-size: 14px;
            border: none;
            text-decoration: none;
            white-space: nowrap; /* Agar teks tidak turun */
            box-shadow: 0 2px 8px rgba(74, 103, 65, 0.15);
        }

        .btn-primary:hover {
            background: #3d5535;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.25);
        }

        /* Responsif untuk Desktop */
        @media (min-width: 768px) {
            .toolbar-container {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            
            .search-form {
                max-width: 400px; /* Batasi lebar search di desktop */
            }
        }

        /* 4. Table Styling */
        .modern-table { width: 100%; border-collapse: collapse; }
        .modern-table thead { background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .modern-table thead th { color: #64748b; font-weight: 600; text-transform: uppercase; padding: 16px; font-size: 11px; letter-spacing: 0.05em; text-align: left; }
        .modern-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.2s ease; }
        .modern-table tbody tr:hover { background: #fcfdfa; }
        .modern-table tbody td { padding: 14px 16px; color: #334155; font-size: 14px; vertical-align: middle; }

        /* 5. Badges */
        .badge-geo { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f1f5f9; color: #475569; font-family: monospace; font-size: 11px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .badge-geo-alt { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; background: #fff7ed; color: #c2410c; font-size: 10px; border-radius: 4px; border: 1px solid #ffedd5; font-weight: 700; margin-top: 4px; }
        .badge-radius { display: inline-block; padding: 4px 10px; background: #ecfdf5; color: #059669; font-weight: 700; font-size: 12px; border-radius: 6px; }
        .badge-teacher { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #fffbeb; color: #b45309; font-size: 12px; font-weight: 600; border-radius: 20px; border: 1px solid #fef3c7; }

        /* 6. Action Buttons */
        .btn-action { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; border: 1px solid #e2e8f0; background: white; margin-right: 4px; }
        .btn-edit { color: #d97706; } .btn-edit:hover { background: #fef3c7; border-color: #fbbf24; }
        .btn-detail { color: #2563eb; } .btn-detail:hover { background: #dbeafe; border-color: #60a5fa; }
        .btn-delete { color: #dc2626; } .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; }

        /* Pagination */
        .pagination-wrapper { padding: 16px; border-top: 1px solid #f1f5f9; }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Manajemen Kelas & Lokasi</span>
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-standard">
        <div class="max-w-7xl mx-auto">
            
            <!-- TOOLBAR YANG SEIMBANG -->
            <div class="toolbar-container">
                <!-- Search Form -->
                <form method="GET" action="{{ route('classrooms.index') }}" class="search-form">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="search-input" 
                           placeholder="Cari nama kelas atau wali kelas...">
                </form>

                <!-- Add Button -->
                <a href="{{ route('classrooms.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Kelas</span>
                </a>
            </div>

            <!-- TABLE CARD -->
            <div class="custom-card bg-white">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-left">Nama Kelas</th>
                                <th class="text-left">Wali Kelas</th>
                                <th class="text-left">Koordinat & Lokasi</th>
                                <th class="text-center">Radius</th>
                                <th class="text-center" style="width: 150px;">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classrooms as $index => $c)
                            <tr>
                                <td class="text-center text-slate-400 font-medium">
                                    {{ $index + 1 + ($classrooms->currentPage() - 1) * $classrooms->perPage() }}
                                </td>
                                
                                <!-- Nama Kelas -->
                                <td>
                                    <span class="font-bold text-slate-800 text-base">{{ $c->name }}</span>
                                    <div class="text-xs text-slate-400 mt-1">Tingkat {{ $c->grade_level }}</div>
                                </td>

                                <!-- Wali Kelas -->
                                <td>
                                    @if($c->homeroomTeacher)
                                        <span class="badge-teacher">
                                            <i class="fa-solid fa-user-tie text-[10px]"></i>
                                            {{ $c->homeroomTeacher->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum diset</span>
                                    @endif
                                </td>

                                <!-- Koordinat (Logic Dual Location) -->
                                <td>
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="badge-geo" title="Lokasi Utama">
                                            <i class="fa-solid fa-location-dot text-green-600"></i>
                                            {{ number_format($c->latitude, 5) }}, {{ number_format($c->longitude, 5) }}
                                        </span>
                                        
                                        @if($c->latitude2 && $c->longitude2)
                                            <span class="badge-geo-alt" title="Lokasi Alternatif Aktif">
                                                <i class="fa-solid fa-map-location-dot"></i> + Lokasi Alternatif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Radius -->
                                <td class="text-center">
                                    <span class="badge-radius">{{ $c->radius_meters }}m</span>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('classrooms.show', $c->id) }}" class="btn-action btn-detail" title="Lihat Detail & Peta">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('classrooms.edit', $c->id) }}" class="btn-action btn-edit" title="Edit Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('classrooms.destroy', $c->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus kelas ini? Data siswa di dalamnya akan kehilangan kelas.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                                        <p>Belum ada data kelas ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                @if($classrooms->hasPages())
                    <div class="pagination-wrapper">
                        {{ $classrooms->appends(['search' => request('search')])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>