<x-app-layout>
    <style>
        /* 1. Animasi Standar Dashboard */
        @keyframes fadeInStandard { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-standard { animation: fadeInStandard 0.4s ease-out forwards; }

        /* 2. Style Header & Card */
        .custom-card { border-radius: 16px !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0 !important; overflow: hidden; background: white; }

        /* 3. Button */
        .btn-primary { background: #4a6741; color: white; border-radius: 12px; font-weight: 600; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 20px; height: 48px; font-size: 14px; border: none; }
        .btn-primary:hover { background: #3d5535; transform: translateY(-1px); }

        /* 4. Table Styling */
        .modern-table { width: 100%; border-collapse: collapse; }
        .modern-table thead { background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .modern-table thead th { color: #64748b; font-weight: 600; text-transform: uppercase; padding: 16px; font-size: 11px; letter-spacing: 0.05em; }
        .modern-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.2s ease; }
        .modern-table tbody tr:hover { background: #f8fafc; }
        .modern-table tbody td { padding: 16px; color: #334155; font-size: 14px; }

        /* 5. Badge & Action Buttons */
        .badge-geo { display: inline-block; padding: 4px 10px; background: #f1f5f9; color: #475569; font-family: monospace; font-size: 12px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .badge-radius { display: inline-block; padding: 4px 10px; background: #ecfdf5; color: #059669; font-weight: 700; font-size: 12px; border-radius: 6px; }
        
        /* Badge Wali Kelas */
        .badge-teacher { 
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 10px; background: #fffbeb; color: #b45309; 
            font-size: 12px; font-weight: 600; border-radius: 20px; border: 1px solid #fef3c7;
        }

        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; border: 1px solid #e2e8f0; background: white; font-size: 14px; }
        .btn-edit { color: #d97706; } .btn-edit:hover { background: #fef3c7; border-color: #fbbf24; }
        .btn-detail { color: #2563eb; } .btn-detail:hover { background: #dbeafe; border-color: #60a5fa; }
        .btn-delete { color: #dc2626; } .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; }

        /* 6. SEARCH BAR STYLING */
        .search-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 24px;
        }

        .search-wrapper {
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            padding: 0 16px;
            height: 48px;
            width: 100%;
        }

        .search-wrapper:focus-within {
            border-color: #4a6741;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }

        .search-icon {
            color: #94a3b8;
            margin-right: 12px;
            font-size: 14px;
        }

        .search-input {
            border: none;
            background: transparent;
            width: 100%;
            font-size: 14px;
            color: #334155;
            outline: none;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .search-input:focus {
            outline: none;
            box-shadow: none;
        }

        .search-stats {
            color: #64748b;
            font-size: 13px;
            margin-top: 4px;
            padding-left: 8px;
        }

        /* 7. PAGINATION CUSTOM STYLES - SAMA DENGAN USERS */
        .custom-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 16px;
            border-top: 1px solid #f1f5f9;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination-info {
            color: #64748b;
            font-size: 13px;
            margin-right: auto;
            padding: 0 8px;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            background: white;
            border: 1px solid #e2e8f0;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .page-link.active {
            background: #4a6741;
            color: white;
            border-color: #4a6741;
            font-weight: 600;
        }

        .page-link.disabled {
            color: #cbd5e1;
            cursor: not-allowed;
            background: #f8fafc;
        }

        .page-link.disabled:hover {
            transform: none;
            background: #f8fafc;
        }

        .page-link.arrow {
            min-width: 36px;
            font-weight: 600;
        }

        /* Ellipsis for pagination */
        .pagination-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            color: #94a3b8;
            font-size: 13px;
        }

        /* Mobile pagination styles */
        @media (max-width: 768px) {
            .custom-pagination {
                justify-content: center;
                gap: 6px;
                padding: 16px 12px;
            }
            
            .pagination-info {
                width: 100%;
                text-align: center;
                margin-bottom: 12px;
                margin-right: 0;
                order: 1;
            }
            
            .page-link {
                min-width: 32px;
                height: 32px;
                padding: 0 8px;
                font-size: 12px;
            }
            
            .pagination-links {
                order: 2;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 4px;
            }
            
            .page-link.hide-on-mobile {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .page-link.arrow {
                min-width: 28px;
                height: 28px;
                font-size: 11px;
            }
            
            .pagination-info {
                font-size: 12px;
            }
            
            .search-container {
                gap: 12px;
            }
            
            .search-wrapper {
                height: 44px;
                padding: 0 12px;
            }
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Manajemen Kelas & Lokasi</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola area absensi dan koordinat ruang kelas</p>
            </div>
            <div class="hidden sm:block">
                {{-- <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200">
                    GEO-FENCING ACTIVE
                </span> --}}
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-standard">
        <div class="max-w-7xl mx-auto">
            
            <!-- SEARCH SECTION -->
            <div class="search-container">
                <form method="GET" action="{{ route('classrooms.index') }}" class="w-full">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari nama kelas atau wali kelas..." 
                            class="search-input"
                        >
                    </div>
                    @if(request('search'))
                        <div class="search-stats">
                            Hasil pencarian untuk "<span class="font-medium text-[#4a6741]">{{ request('search') }}</span>"
                        </div>
                    @endif
                </form>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex justify-between items-center mb-6">
                <div class="text-sm text-slate-500">
                    Total: <span class="font-semibold">{{ $classrooms->total() }}</span> kelas
                </div>
                <a href="{{ route('classrooms.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Kelas Baru</span>
                </a>
            </div>

            <!-- TABLE SECTION -->
            <div class="custom-card bg-white">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-left">Nama Kelas</th>
                                <th class="text-left">Wali Kelas</th>
                                <th class="text-left">Titik Koordinat</th>
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
                                    <span class="font-bold text-slate-800">{{ $c->name }}</span>
                                    <div class="text-xs text-slate-400 mt-1">Tingkat {{ $c->grade_level }}</div>
                                </td>

                                <!-- Wali Kelas -->
                                <td>
                                    @if($c->homeroomTeacher)
                                        <span class="badge-teacher">
                                            <i class="fa-solid fa-chalkboard-user"></i>
                                            {{ $c->homeroomTeacher->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum diset</span>
                                    @endif
                                </td>

                                <!-- Koordinat -->
                                <td>
                                    <span class="badge-geo">
                                        <i class="fa-solid fa-location-dot mr-1 opacity-50"></i>
                                        {{ number_format($c->latitude, 5) }}, {{ number_format($c->longitude, 5) }}
                                    </span>
                                </td>

                                <!-- Radius -->
                                <td class="text-center">
                                    <span class="badge-radius">{{ $c->radius_meters }}m</span>
                                </td>

                                <!-- Aksi -->
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('classrooms.show', $c->id) }}" class="btn-action btn-detail" title="Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('classrooms.edit', $c->id) }}" class="btn-action btn-edit" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('classrooms.destroy', $c->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus kelas ini?')">
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
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-folder-open text-4xl mb-3 block opacity-20"></i>
                                    @if(request('search'))
                                        Tidak ditemukan kelas dengan kata kunci "<span class="font-medium">{{ request('search') }}</span>"
                                    @else
                                        Belum ada data kelas.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION SECTION -->
                @if($classrooms->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-info">
                        Menampilkan {{ $classrooms->firstItem() ?? 0 }} - {{ $classrooms->lastItem() ?? 0 }} dari {{ $classrooms->total() }}
                    </div>
                    
                    <div class="pagination-links">
                        {{-- Previous Page Link --}}
                        @if ($classrooms->onFirstPage())
                            <span class="page-link arrow disabled">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $classrooms->appends(['search' => request('search')])->previousPageUrl() }}" class="page-link arrow">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $current = $classrooms->currentPage();
                            $last = $classrooms->lastPage();
                            $dots = false;
                        @endphp

                        @for ($i = 1; $i <= $last; $i++)
                            @if ($i == 1 || $i == $last || ($i >= $current - 1 && $i <= $current + 1))
                                @if ($i == $current)
                                    <span class="page-link active">{{ $i }}</span>
                                @else
                                    <a href="{{ $classrooms->appends(['search' => request('search')])->url($i) }}" 
                                       class="page-link {{ ($i > 2 && $i < $last - 1) ? 'hide-on-mobile' : '' }}">
                                        {{ $i }}
                                    </a>
                                @endif
                                @php $dots = false; @endphp
                            @elseif (!$dots)
                                <span class="pagination-ellipsis hide-on-mobile">...</span>
                                @php $dots = true; @endphp
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($classrooms->hasMorePages())
                            <a href="{{ $classrooms->appends(['search' => request('search')])->nextPageUrl() }}" class="page-link arrow">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="page-link arrow disabled">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debounce untuk search input
            let searchTimeout;
            const searchInput = document.querySelector('input[name="search"]');
            
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    
                    // Submit form setelah 500ms tanpa ketikan
                    searchTimeout = setTimeout(function() {
                        e.target.closest('form').submit();
                    }, 500);
                });
                
                // Submit form saat enter ditekan
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.target.closest('form').submit();
                    }
                });
            }
        });
    </script>
</x-app-layout>