<x-app-layout>
    <x-slot name="header"></x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --asparagus:  #8DA750;
        }

        /* Card Riwayat */
        .history-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 16px;
            border: 1px solid #f3f4f6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
            transition: 0.2s;
        }
        .history-card:active { transform: scale(0.98); }

        /* Garis Warna Status di Kiri Card */
        .status-line { position: absolute; left: 0; top: 0; bottom: 0; width: 6px; }
        .status-pending { background-color: #fbbf24; } /* Kuning */
        .status-approved { background-color: var(--fern); } /* Hijau */
        .status-rejected { background-color: #ef4444; } /* Merah */

        /* Badge */
        .badge {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.05em;
        }
        .bg-pending { background: #fffbeb; color: #b45309; }
        .bg-approved { background: #f0fdf4; color: var(--fern); }
        .bg-rejected { background: #fef2f2; color: #b91c1c; }

        .btn-add-float {
            position: fixed;
            bottom: 30px; right: 30px;
            background: var(--cal-poly);
            color: white;
            width: 60px; height: 60px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 10px 25px rgba(45,81,40,0.4);
            transition: 0.3s;
            z-index: 50;
        }
        .btn-add-float:hover { transform: scale(1.1); }

        /* PAGINATION CUSTOM STYLES - SAMA DENGAN SEBELUMNYA */
        .custom-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 16px;
            border-top: 1px solid #f1f5f9;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 20px;
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

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-md mx-auto relative pb-20">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#142C14]">Riwayat Izin</h1>
                    <p class="text-sm text-gray-500">Daftar pengajuan ketidakhadiran Anda.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-[#537B2F]">Kembali</a>
            </div>

            <!-- List Card -->
            @forelse($requests as $index => $req)
                <div class="history-card">
                    <!-- Garis Indikator Status -->
                    <div class="status-line status-{{ $req->status }}"></div>

                    <div class="pl-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg capitalize">
                                    {{ $req->type == 'sick' ? '🤒 Sakit' : '✉️ Izin' }}
                                </h3>
                                <p class="text-xs text-gray-400 font-mono mt-1">
                                    {{ \Carbon\Carbon::parse($req->created_at)->format('d M Y, H:i') }}
                                </p>
                            </div>
                            
                            <!-- Badge Status -->
                            @if($req->status == 'pending')
                                <span class="badge bg-pending">Menunggu</span>
                            @elseif($req->status == 'approved')
                                <span class="badge bg-approved">Disetujui</span>
                            @else
                                <span class="badge bg-rejected">Ditolak</span>
                            @endif
                        </div>

                        <!-- Detail Tanggal -->
                        <div class="bg-gray-50 p-3 rounded-lg mt-3 mb-3 border border-gray-100">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Tanggal:</span>
                                <span class="font-bold text-gray-700">
                                    {{ \Carbon\Carbon::parse($req->start_date)->format('d/m') }} 
                                    - 
                                    {{ \Carbon\Carbon::parse($req->end_date)->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Alasan -->
                        <p class="text-sm text-gray-600 line-clamp-2 italic">
                            "{{ $req->reason }}"
                        </p>

                        <!-- Catatan Guru (Jika Ada) -->
                        @if($req->notes)
                            <div class="mt-3 pt-3 border-t border-dashed border-gray-200">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Catatan Wali Kelas:</p>
                                <p class="text-sm text-gray-700">{{ $req->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 opacity-60">
                    <i class="fa-regular fa-folder-open text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada riwayat pengajuan.</p>
                </div>
            @endforelse

            <!-- PAGINATION SECTION -->
            @if($requests->hasPages())
            <div class="custom-pagination">
                <div class="pagination-info">
                    Menampilkan {{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }} dari {{ $requests->total() }}
                </div>
                
                <div class="pagination-links">
                    {{-- Previous Page Link --}}
                    @if ($requests->onFirstPage())
                        <span class="page-link arrow disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $requests->previousPageUrl() }}" class="page-link arrow">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $current = $requests->currentPage();
                        $last = $requests->lastPage();
                        $dots = false;
                    @endphp

                    @for ($i = 1; $i <= $last; $i++)
                        @if ($i == 1 || $i == $last || ($i >= $current - 1 && $i <= $current + 1))
                            @if ($i == $current)
                                <span class="page-link active">{{ $i }}</span>
                            @else
                                <a href="{{ $requests->url($i) }}" 
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
                    @if ($requests->hasMorePages())
                        <a href="{{ $requests->nextPageUrl() }}" class="page-link arrow">
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

            <!-- Tombol Floating Add (Pojok Kanan Bawah) -->
            <a href="{{ route('leaves.create') }}" class="btn-add-float">
                <i class="fa-solid fa-plus"></i>
            </a>

        </div>
    </div>
</x-app-layout>