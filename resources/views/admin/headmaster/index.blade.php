<x-app-layout>
        <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Validasi Pengajuan Staff</span>
            </h2>
        </div>
    </x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --asparagus:  #8DA750;
            --mindaro:    #E4EB9C;
            --cream:      #FAFAF5;
        }

        /* Banner Style */
        .welcome-banner {
            background-color: var(--cal-poly);
            border-radius: 20px;
            padding: 24px 32px;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(45, 81, 40, 0.3);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* List Pengajuan Style (Pending) */
        .request-item {
            border: 1px solid #eef2eb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            transition: 0.2s;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .request-item:hover {
            border-color: var(--mindaro);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* List Riwayat Style (History) */
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            background: white;
        }
        .history-item:last-child { border-bottom: none; }
        .history-item:hover { background: #f8fafc; }

        /* Tombol Aksi */
        .btn-approve {
            background: var(--fern);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-approve:hover { background: var(--dark-green); }

        .btn-reject {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-reject:hover { background: #fecaca; }

        /* Badge Tipe & Status */
        .badge-sick { background: #fffbeb; color: #b45309; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .badge-perm { background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        
        .status-approved { color: var(--fern); font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; gap: 4px; }
        .status-rejected { color: #dc2626; font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; gap: 4px; }

        .section-title {
            color: var(--dark-green);
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-6xl mx-auto">
            
            <!-- Banner Kepala Sekolah -->
            <div class="welcome-banner">
                <div>
                    <p class="text-[var(--mindaro)] text-xs font-bold uppercase tracking-widest mb-1">Area Kepala Sekolah</p>
                    <h1 class="text-2xl font-black">Validasi Izin Guru</h1>
                    <p class="text-white/80 text-sm mt-1">Kelola permohonan izin dan sakit dari tenaga pengajar.</p>
                </div>
                <div class="text-right hidden md:block">
                    <div class="text-4xl font-black text-white">{{ $pendingLeaves->count() }}</div>
                    <div class="text-xs text-white/70 font-bold uppercase">Menunggu</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KOLOM KIRI (UTAMA): DAFTAR PENGAJUAN -->
                <div class="lg:col-span-2">
                    <h3 class="section-title">
                        <i class="fa-solid fa-inbox text-[var(--fern)]"></i> Permohonan Masuk
                    </h3>

                    @forelse($pendingLeaves as $req)
                        <div class="request-item">
                            <!-- Header Item -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-4">
                                    <!-- Avatar Inisial -->
                                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-lg border border-blue-100">
                                        {{ substr($req->student->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-base">{{ $req->student->name }}</h4>
                                        <span class="text-xs text-gray-400 font-mono">NIP: {{ $req->student->nip_nis ?? '-' }}</span>
                                    </div>
                                </div>
                                <span class="{{ $req->type == 'sick' ? 'badge-sick' : 'badge-perm' }}">
                                    {{ $req->type == 'sick' ? 'Sakit' : 'Izin' }}
                                </span>
                            </div>

                            <!-- Detail Tanggal & Alasan -->
                            <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl text-sm text-gray-600 mb-4">
                                <div class="flex justify-between mb-2 pb-2 border-b border-gray-200">
                                    <span>Mulai: <strong class="text-gray-800">{{ \Carbon\Carbon::parse($req->start_date)->format('d M Y') }}</strong></span>
                                    <span>Sampai: <strong class="text-gray-800">{{ \Carbon\Carbon::parse($req->end_date)->format('d M Y') }}</strong></span>
                                </div>
                                <p class="italic">"{{ $req->reason }}"</p>
                            </div>

                            <!-- Bukti Foto -->
                            @if($req->attachment)
                                <div class="mb-4">
                                    <a href="{{ asset('storage/'.$req->attachment) }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:underline bg-blue-50 px-3 py-2 rounded-lg">
                                        <i class="fa-solid fa-paperclip"></i> Lihat Bukti Lampiran
                                    </a>
                                </div>
                            @endif

                            <!-- Form Aksi -->
                            <form action="{{ route('headmaster.update', $req->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <input type="text" name="notes" placeholder="Tambahkan catatan persetujuan/penolakan (opsional)..." class="w-full text-sm border border-gray-200 rounded-xl p-3 focus:outline-none focus:border-[var(--fern)] focus:ring-1 focus:ring-[var(--fern)]">
                                </div>
                                <div class="flex gap-3">
                                    <button type="submit" name="action" value="approve" class="btn-approve flex-1">
                                        <i class="fa-solid fa-check"></i> Setujui
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn-reject flex-1">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-12 opacity-60 bg-white rounded-xl border border-dashed border-gray-300">
                            <i class="fa-regular fa-circle-check text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">Tidak ada permohonan baru.</p>
                            <p class="text-xs text-gray-400">Semua izin guru sudah diproses.</p>
                        </div>
                    @endforelse
                </div>

                <!-- KOLOM KANAN: RIWAYAT -->
                <div>
                    <h3 class="section-title">
                        <i class="fa-solid fa-clock-rotate-left text-[var(--asparagus)]"></i> Riwayat Proses
                    </h3>

                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                        @forelse($historyLeaves as $hist)
                            <div class="history-item">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar Kecil -->
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                        {{ substr($hist->student->name, 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-bold text-gray-700 truncate w-32" title="{{ $hist->student->name }}">{{ $hist->student->name }}</p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ $hist->type == 'sick' ? 'Sakit' : 'Izin' }} • {{ \Carbon\Carbon::parse($hist->start_date)->format('d/m') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    @if($hist->status == 'approved')
                                        <div class="status-approved justify-end">
                                            Disetujui <i class="fa-solid fa-check-circle"></i> 
                                        </div>
                                    @else
                                        <div class="status-rejected justify-end">
                                            Ditolak <i class="fa-solid fa-circle-xmark"></i>
                                        </div>
                                    @endif
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        {{ $hist->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-gray-400 italic">
                                Belum ada riwayat proses.
                            </div>
                        @endforelse

                        <!-- Pagination Link -->
                        @if($historyLeaves->hasPages())
                            <div class="p-3 border-t border-gray-100 bg-gray-50 flex justify-center">
                                {{ $historyLeaves->links() }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>