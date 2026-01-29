<x-app-layout>

        <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Manajemen Tahun Ajaran</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Atur rentang waktu akademik dan pantau status periode pembelajaran saat ini. 
                </p>
            </div>
        </div>
    </x-slot>

    <style>
        :root { --dark-green: #2D5128; --mid-green: #537B2F; --light-green: #8DA750; }
        .page-title { color: var(--dark-green); font-weight: 800; font-size: 1.5rem; }
        .card-custom { background: white; border-radius: 16px; border: 1px solid #f0fdf4; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 24px; }
        .btn-green { background: var(--mid-green); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; transition: 0.2s; }
        .btn-green:hover { background: var(--dark-green); }
        .table-custom th { text-align: left; color: var(--light-green); text-transform: uppercase; font-size: 0.75rem; padding: 12px; border-bottom: 2px solid #f0fdf4; }
        .table-custom td { padding: 16px 12px; border-bottom: 1px solid #f7fee7; color: #333; }
        .active-badge { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; border: 1px solid #bbf7d0; }
        .inactive-badge { background: #f3f4f6; color: #6b7280; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- KOLOM KIRI: FORM TAMBAH -->
            <div class="lg:col-span-1">
                <div class="card-custom">
                    <h3 class="text-lg font-bold text-[#2D5128] mb-4">Buat Tahun Ajaran Baru</h3>
                    <form action="{{ route('academic-years.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Tahun Ajar</label>
                            <input type="text" name="name" placeholder="Contoh: 2025/2026 Ganjil" class="w-full rounded-lg border-gray-300 focus:ring-[#537B2F] focus:border-[#537B2F]" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="w-full rounded-lg border-gray-300" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="w-full rounded-lg border-gray-300" required>
                        </div>
                        <button type="submit" class="btn-green w-full">Simpan Data</button>
                    </form>
                </div>
            </div>

            <!-- KOLOM KANAN: LIST DATA -->
            <div class="lg:col-span-2">
                <div class="card-custom">
                    <h2 class="page-title mb-6">Daftar Tahun Ajaran</h2>
                    
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="table-custom w-full">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Nama</th>
                                    <th>Periode</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($years as $y)
                                <tr>
                                    <td>
                                        @if($y->is_active)
                                            <span class="active-badge">AKTIF ●</span>
                                        @else
                                            <span class="inactive-badge">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="font-bold">{{ $y->name }}</td>
                                    <td class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($y->start_date)->format('d M Y') }} <br>
                                        s/d <br>
                                        {{ \Carbon\Carbon::parse($y->end_date)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            @if(!$y->is_active)
                                                <form action="{{ route('academic-years.set-active', $y->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200 font-bold" title="Aktifkan ini">
                                                        Set Aktif
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('academic-years.destroy', $y->id) }}" method="POST" onsubmit="return confirm('Hapus tahun ajar ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Sedang Digunakan</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-400">Belum ada data. Silakan input di form sebelah kiri.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>