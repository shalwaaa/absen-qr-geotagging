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
            @forelse($requests as $req)
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

            <!-- Tombol Floating Add (Pojok Kanan Bawah) -->
            <a href="{{ route('leaves.create') }}" class="btn-add-float">
                <i class="fa-solid fa-plus"></i>
            </a>

        </div>
    </div>
</x-app-layout>