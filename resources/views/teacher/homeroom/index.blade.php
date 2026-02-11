<x-app-layout>
       <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Area Wali Kelas</span>
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

        /* Card Style */
        .content-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #f0fdf4;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        /* List Pengajuan Style (Pending) */
        .request-item {
            border: 1px solid #eef2eb;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            transition: 0.2s;
            background: #FAFCF8;
        }
        .request-item:hover {
            border-color: var(--mindaro);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        /* List Riwayat Style (History) - Lebih Tipis */
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
        .btn-approve { background: var(--fern); color: white; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer; transition: 0.2s; }
        .btn-approve:hover { background: var(--dark-green); }

        .btn-reject { background: #fee2e2; color: #b91c1c; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer; transition: 0.2s; }
        .btn-reject:hover { background: #fecaca; }

        /* Badge Tipe & Status */
        .badge-sick { background: #fffbeb; color: #b45309; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .badge-perm { background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        
        .status-approved { color: var(--fern); font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; gap: 4px; }
        .status-rejected { color: #dc2626; font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; gap: 4px; }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-6xl mx-auto">
            
            <!-- Banner Wali Kelas -->
            <div class="welcome-banner">
                <div>
                    <h1 class="text-2xl font-black">Kelas {{ $classroom->name }}</h1>
                    <p class="text-white/80 text-sm mt-1">Kelola perizinan siswa Anda di sini.</p>
                </div>
                <div class="text-right hidden md:block">
                    <div class="text-4xl font-black text-white">{{ $pendingLeaves->count() }}</div>
                    <div class="text-xs text-white/70 font-bold uppercase">Permintaan Baru</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KOLOM KIRI (UTAMA): DAFTAR PENGAJUAN & RIWAYAT -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- 1. PENGAJUAN BARU (PRIORITAS) -->
                    <div>
                        <h3 class="text-lg font-bold text-[var(--dark-green)] mb-4 flex items-center gap-2">
                            <div class="w-2 h-6 bg-[var(--fern)] rounded-full"></div>
                            Pengajuan Masuk
                        </h3>

                        @forelse($pendingLeaves as $req)
                            <div class="request-item">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-500">
                                            {{ substr($req->student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $req->student->name }}</h4>
                                            <span class="text-xs text-gray-400">NIS: {{ $req->student->nip_nis }}</span>
                                        </div>
                                    </div>
                                    <span class="{{ $req->type == 'sick' ? 'badge-sick' : 'badge-perm' }}">
                                        {{ $req->type == 'sick' ? 'Sakit' : 'Izin' }}
                                    </span>
                                </div>

                                <div class="bg-white border border-gray-100 p-3 rounded-lg text-sm text-gray-600 mb-3">
                                    <p class="mb-1"><span class="font-bold text-gray-800">Tanggal:</span> 
                                        {{ \Carbon\Carbon::parse($req->start_date)->format('d/m') }} - 
                                        {{ \Carbon\Carbon::parse($req->end_date)->format('d/m/Y') }}
                                    </p>
                                    <p class="italic">"{{ $req->reason }}"</p>
                                </div>

                                @if($req->attachment)
                                    <a href="{{ asset('storage/'.$req->attachment) }}" target="_blank" class="text-xs text-blue-600 hover:underline mb-3 block">
                                        <i class="fa-solid fa-paperclip"></i> Lihat Bukti Lampiran
                                    </a>
                                @endif

                                <form action="{{ route('homeroom.update', $req->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="text" name="notes" placeholder="Catatan untuk siswa (opsional)..." class="w-full text-xs border border-gray-200 rounded-lg p-2 focus:outline-none focus:border-[var(--fern)]">
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" name="action" value="approve" class="btn-approve flex-1">
                                            <i class="fa-solid fa-check"></i> Terima
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn-reject flex-1">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-8 opacity-50 bg-white rounded-xl border border-dashed border-gray-300">
                                <i class="fa-regular fa-circle-check text-3xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500 text-sm">Semua pengajuan sudah diproses.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- 2. RIWAYAT PENGAJUAN (HISTORY) - FITUR BARU -->
 <!-- 2. RIWAYAT PENGAJUAN (HISTORY) -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-[var(--dark-green)] flex items-center gap-2">
                                <div class="w-2 h-6 bg-gray-300 rounded-full"></div>
                                Riwayat Proses
                            </h3>
                            <!-- Info Halaman Kecil -->
                            <span class="text-xs text-gray-400">
                                Hal {{ $historyLeaves->currentPage() }} dari {{ $historyLeaves->lastPage() }}
                            </span>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            @forelse($historyLeaves as $hist)
                                <div class="history-item">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                            {{ substr($hist->student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-700">{{ $hist->student->name }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ $hist->type == 'sick' ? 'Sakit' : 'Izin' }} • {{ \Carbon\Carbon::parse($hist->start_date)->format('d/m') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        @if($hist->status == 'approved')
                                            <div class="status-approved">
                                                <i class="fa-solid fa-check-circle"></i> Disetujui
                                            </div>
                                        @else
                                            <div class="status-rejected">
                                                <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                            </div>
                                        @endif
                                        <p class="text-[10px] text-gray-400 text-right mt-1">
                                            {{ $hist->updated_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-gray-400">Belum ada riwayat proses.</div>
                            @endforelse

                            <!-- PAGINATION LINK -->
                            @if($historyLeaves->hasPages())
                                <div class="p-4 border-t border-gray-100">
                                    {{ $historyLeaves->links() }}
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- KOLOM KANAN: DATA SISWA -->
                <div>
                    <div class="content-card sticky top-6">
                        <h3 class="text-sm font-bold text-[var(--dark-green)] uppercase tracking-widest mb-4 border-b pb-2">
                            Daftar Siswa
                        </h3>
                        <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1 custom-scrollbar">
                            @foreach($students as $s)
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                        <span class="text-sm text-gray-700 font-medium">{{ $s->name }}</span>
                                    </div>
                                    <!-- Jika ingin menampilkan status hari ini (opsional, butuh query tambahan) -->
                                    <!-- <span class="text-xs text-gray-400">Hadir</span> -->
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>