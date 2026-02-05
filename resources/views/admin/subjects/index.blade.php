<x-app-layout>
    <style>
        /* 1. Animasi Standar */
        @keyframes fadeInStandard {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-standard {
            animation: fadeInStandard 0.4s ease-out forwards;
        }

        /* 2. Style Card & Header */
        .header-section {
            animation: fadeInStandard 0.4s ease-out;
        }

        .custom-card {
            border-radius: 16px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0 !important;
            overflow: hidden;
            background: white;
        }

        /* 3. Button Standar Dashboard */
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
            padding: 0 20px;
            height: 48px;
            font-size: 14px;
            border: none;
        }

        .btn-primary:hover {
            background: #3d5535;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2);
        }

        /* 4. Table Styling */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table thead {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table thead th {
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            padding: 16px;
            font-size: 11px;
            letter-spacing: 0.05em;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        .modern-table tbody td {
            padding: 16px;
            font-size: 14px;
        }

        /* 5. Khusus Mapel: Code Badge */
        .subject-code {
            display: inline-block;
            padding: 2px 8px;
            background: #f1f5f9;
            color: #475569;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }

        /* 6. Action Buttons */
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            background: white;
            font-size: 14px;
        }

        .btn-edit { color: #d97706; }
        .btn-edit:hover { background: #fef3c7; border-color: #fbbf24; }

        .btn-delete { color: #dc2626; }
        .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; }

        /* 7. PAGINATION CUSTOM STYLES - SAMA DENGAN SEBELUMNYA */
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
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Data Mata Pelajaran</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola daftar mata pelajaran dan kode kurikulum
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-standard">
        <div class="max-w-7xl mx-auto">
            
            <div class="flex justify-end mb-6">
                <a href="{{ route('subjects.create') }}" class="btn-primary shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Mapel</span>
                </a>
            </div>

            <div class="custom-card">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">No</th>
                                <th class="text-center" style="width: 150px;">Kode</th>
                                <th class="text-left">Nama Mata Pelajaran</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjects as $index => $s)
                            <tr>
                                <td class="text-center text-slate-400 font-medium">
                                    {{ $index + 1 + ($subjects->currentPage() - 1) * $subjects->perPage() }}
                                </td>
                                <td class="text-center">
                                    <span class="subject-code">
                                        {{ $s->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-700 uppercase tracking-tight">
                                        {{ $s->name }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('subjects.edit', $s->id) }}" class="btn-action btn-edit" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        
                                        <form action="{{ route('subjects.destroy', $s->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus mapel ini? Jadwal terkait akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center text-slate-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-book-open text-5xl mb-4 opacity-10"></i>
                                        <p class="text-lg font-medium">Belum ada data mata pelajaran</p>
                                        <p class="text-sm opacity-70">Silahkan tambah mata pelajaran baru untuk memulai.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION SECTION -->
                @if($subjects->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-info">
                        Menampilkan {{ $subjects->firstItem() ?? 0 }} - {{ $subjects->lastItem() ?? 0 }} dari {{ $subjects->total() }}
                    </div>
                    
                    <div class="pagination-links">
                        {{-- Previous Page Link --}}
                        @if ($subjects->onFirstPage())
                            <span class="page-link arrow disabled">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $subjects->previousPageUrl() }}" class="page-link arrow">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $current = $subjects->currentPage();
                            $last = $subjects->lastPage();
                            $dots = false;
                        @endphp

                        @for ($i = 1; $i <= $last; $i++)
                            @if ($i == 1 || $i == $last || ($i >= $current - 1 && $i <= $current + 1))
                                @if ($i == $current)
                                    <span class="page-link active">{{ $i }}</span>
                                @else
                                    <a href="{{ $subjects->url($i) }}" 
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
                        @if ($subjects->hasMorePages())
                            <a href="{{ $subjects->nextPageUrl() }}" class="page-link arrow">
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

            <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-slate-400 mt-0.5"></i>
                <div class="text-xs text-slate-500 leading-relaxed">
                    <strong>Catatan:</strong> Penghapusan mata pelajaran akan berdampak pada jadwal pelajaran yang sudah terdaftar. Pastikan tidak ada jadwal aktif yang menggunakan mata pelajaran tersebut sebelum menghapus.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>